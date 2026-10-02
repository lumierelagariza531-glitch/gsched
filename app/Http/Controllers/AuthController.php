<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            ActivityLog::log('LOGIN', 'User logged in', 'Authentication');

            return match ($user->role?->name) {
                'student' => redirect()->intended(route('student.dashboard')),
                'guidance_associate' => redirect()->intended(route('guidance.dashboard')),
                'admin' => redirect()->intended(route('admin.dashboard')),
                default => redirect()->route('login')->withErrors(['email' => 'Invalid role']),
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'student_id' => ['required', 'string', 'regex:/^\d{2}-\d-\d{4,5}$/', 'unique:users,student_id'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'school' => ['required', 'in:STCS,SNHS,SCJE,STED,SAS,SME,SOE,SBM'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'gender' => ['required', 'in:male,female,prefer_not_to_say'],
            'student_id_front' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=300,min_height=200,max_width=6000,max_height=6000'],
            'student_id_back' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=300,min_height=200,max_width=6000,max_height=6000'],
            'screening_passed' => ['required', 'accepted'],
        ]);

        $dateOfBirth = Carbon::parse($request->date_of_birth);
        if ($dateOfBirth->age < 12 || $dateOfBirth->age > 100) {
            return back()->withErrors(['date_of_birth' => 'You must be between 12 and 100 years old to register.'])->withInput();
        }

        $studentRole = Role::where('name', 'student')->firstOrFail();
        $storedPaths = [];
        $proofDirectory = null;

        try {
            $user = DB::transaction(function () use ($request, $studentRole, $dateOfBirth, &$storedPaths, &$proofDirectory) {
                $user = User::create([
                    'role_id' => $studentRole->id,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'student_id' => $request->student_id,
                    'school' => $request->school,
                    'date_of_birth' => $dateOfBirth->toDateString(),
                    'age' => $dateOfBirth->age,
                    'gender' => $request->gender,
                    'password' => Hash::make($request->password),
                    'status' => 'active',
                ]);

                $proofDirectory = "student-id-proofs/{$user->id}";
                $proofPaths = [];
                foreach (['front', 'back'] as $side) {
                    $upload = $request->file("student_id_{$side}");
                    $extension = $upload->guessExtension();
                    if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                        throw new \RuntimeException('Unsupported student ID image format.');
                    }

                    $path = Storage::disk('local')->putFileAs(
                        $proofDirectory,
                        $upload,
                        "{$side}.{$extension}"
                    );
                    if (! $path) {
                        throw new \RuntimeException('Student ID image could not be saved.');
                    }

                    $storedPaths[] = $path;
                    $proofPaths["student_id_{$side}"] = $path;
                }

                $user->fill($proofPaths)->saveOrFail();
                ActivityLog::log('REGISTER', "New student registered: {$user->full_name}", 'Authentication', $user->id);

                return $user;
            });
        } catch (Throwable $exception) {
            if ($proofDirectory) {
                Storage::disk('local')->deleteDirectory($proofDirectory);
            }
            report($exception);

            return back()
                ->withErrors(['student_id_front' => 'We could not securely save your ID images. Please try again.'])
                ->withInput();
        }

        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::log('LOGOUT', 'User logged out', 'Authentication', $user->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showProfile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $isStudent = $user->isStudent();
        $canEditFacebookProfile = $user->isAdmin() || $user->isGuidanceAssociate();

        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ];

        if ($isStudent) {
            $rules['school'] = ['required', 'in:STCS,SNHS,SCJE,STED,SAS,SME,SOE,SBM'];
            $rules['age'] = ['required', 'integer', 'min:12', 'max:100'];
            $rules['gender'] = ['required', 'in:male,female,prefer_not_to_say'];
        } else {
            $rules['school'] = ['nullable', 'string', 'max:255'];
            if ($canEditFacebookProfile) {
                $rules['facebook_profile_url'] = [
                    'nullable',
                    'string',
                    'max:2048',
                    'url',
                    function ($attribute, $value, $fail) {
                        if (!$value) {
                            return;
                        }

                        $parts = parse_url($value);
                        if (
                            strtolower($parts['scheme'] ?? '') !== 'https'
                            || !in_array(strtolower($parts['host'] ?? ''), ['facebook.com', 'www.facebook.com', 'm.facebook.com'], true)
                        ) {
                            $fail('Enter an HTTPS Facebook profile URL.');
                        }
                    },
                ];
            }
            $rules['professional_title'] = ['nullable', 'string', 'max:255'];
            $rules['educational_background'] = ['nullable', 'string'];
            $rules['professional_credentials'] = ['nullable', 'string'];
            $rules['certifications'] = ['nullable', 'string'];
            $rules['trainings'] = ['nullable', 'string'];
            $rules['areas_of_expertise'] = ['nullable', 'string'];
            $rules['professional_experience'] = ['nullable', 'string'];
            $rules['professional_biography'] = ['nullable', 'string'];
            $rules['office_location'] = ['nullable', 'string', 'max:255'];
        }

        $request->validate($rules);

        $updateData = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'school' => $request->school,
        ];

        if ($isStudent) {
            $updateData['age'] = $request->age;
            $updateData['gender'] = $request->gender;
        } else {
            if ($canEditFacebookProfile) {
                $updateData['facebook_profile_url'] = $request->facebook_profile_url;
            }
            $updateData['professional_title'] = $request->professional_title;
            $updateData['educational_background'] = $request->educational_background;
            $updateData['professional_credentials'] = $request->professional_credentials;
            $updateData['certifications'] = $request->certifications;
            $updateData['trainings'] = $request->trainings;
            $updateData['areas_of_expertise'] = $request->areas_of_expertise;
            $updateData['professional_experience'] = $request->professional_experience;
            $updateData['professional_biography'] = $request->professional_biography;
            $updateData['office_location'] = $request->office_location;
        }

        if ($request->input('remove_photo') == '1') {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $updateData['profile_photo'] = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $updateData['profile_photo'] = $path;
        }

        $user->update($updateData);

        ActivityLog::log('UPDATE_PROFILE', "Updated profile: {$user->full_name} ({$user->email})", 'User Management', $user->id);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function showChangePassword()
    {
        return redirect()->to(route('profile').'#password-settings');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log('CHANGE_PASSWORD', 'User changed password', 'Authentication', $user->id);

        return back()->with('success', 'Password changed successfully!');
    }
}
