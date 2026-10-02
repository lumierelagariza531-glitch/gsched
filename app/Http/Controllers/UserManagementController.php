<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $isStudent = Role::find($request->input('role_id'))?->name === 'student';

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'student_id' => ['nullable', 'string', 'regex:/^\d{2}-\d-\d{4,5}$/', 'unique:users,student_id'],
            'school' => $isStudent ? ['required', 'in:STCS,SNHS,SCJE,STED,SAS,SME,SOE,SBM'] : ['nullable', 'in:STCS,SNHS,SCJE,STED,SAS,SME,SOE,SBM'],
            'age' => $isStudent ? ['required', 'integer', 'min:12', 'max:100'] : ['nullable', 'integer', 'min:12', 'max:100'],
            'gender' => $isStudent ? ['required', 'in:male,female,prefer_not_to_say'] : ['nullable', 'in:male,female,prefer_not_to_say'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'role_id' => $request->role_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'school' => $request->school,
            'age' => $request->age,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        ActivityLog::log('CREATE_USER', "Created user: {$user->full_name} ({$user->email})", 'User Management', Auth::id());

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->withErrors(['error' => 'You cannot edit your own account from here.']);
        }

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->withErrors(['error' => 'You cannot edit your own account from here.']);
        }

        $isStudent = $user->role?->name === 'student';

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'student_id' => ['nullable', 'string', 'regex:/^\d{2}-\d-\d{4,5}$/', 'unique:users,student_id,' . $user->id],
            'school' => $isStudent ? ['required', 'in:STCS,SNHS,SCJE,STED,SAS,SME,SOE,SBM'] : ['nullable', 'in:STCS,SNHS,SCJE,STED,SAS,SME,SOE,SBM'],
            'age' => $isStudent ? ['required', 'integer', 'min:12', 'max:100'] : ['nullable', 'integer', 'min:12', 'max:100'],
            'gender' => $isStudent ? ['required', 'in:male,female,prefer_not_to_say'] : ['nullable', 'in:male,female,prefer_not_to_say'],
            'status' => 'required|in:active,inactive',
        ]);

        $user->update([
            'role_id' => $request->role_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'school' => $request->school,
            'age' => $request->age,
            'gender' => $request->gender,
            'status' => $request->status,
        ]);

        ActivityLog::log('UPDATE_USER', "Updated user: {$user->full_name} ({$user->email})", 'User Management', Auth::id());

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        if ($user->role && $user->role->name === 'admin') {
            $adminCount = User::whereHas('role', function ($q) { $q->where('name', 'admin'); })->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['error' => 'Cannot delete the last admin user.']);
            }
        }

        $name = $user->full_name;
        $user->delete();

        ActivityLog::log('DELETE_USER', "Deleted user: {$name}", 'User Management', Auth::id());

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log('RESET_PASSWORD', "Reset password for user: {$user->full_name}", 'User Management', Auth::id());

        return back()->with('success', 'Password reset successfully!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot change your own status.']);
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        ActivityLog::log('TOGGLE_USER_STATUS', "Changed status for user: {$user->full_name} to {$user->status}", 'User Management', Auth::id());

        return back()->with('success', 'User status updated!');
    }
}