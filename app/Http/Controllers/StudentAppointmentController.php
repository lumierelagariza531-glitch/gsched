<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Availability;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class StudentAppointmentController extends Controller
{
    public function schedules()
    {
        $student = Auth::user();
        $guidanceProviders = $this->scheduleProvidersFor($student);
        $providerIds = $guidanceProviders->pluck('id');

        $availableDates = Availability::where('status', 'available')
            ->where('available_date', '>=', Carbon::today())
            ->whereIn('guidance_associate_id', $providerIds)
            ->with('guidanceAssociate.role')
            ->orderBy('available_date')
            ->get()
            ->groupBy('available_date');

        $studentSchool = $student->school;

        return view('student.schedules', compact('availableDates', 'guidanceProviders', 'studentSchool'));
    }

    public function getSlots(Request $request, $date)
    {
        $date = Carbon::parse($date);
        $guidanceProviders = $this->scheduleProvidersFor(Auth::user());

        $availabilities = Availability::where('status', 'available')
            ->where('available_date', $date)
            ->whereIn('guidance_associate_id', $guidanceProviders->pluck('id'))
            ->with('guidanceAssociate.role')
            ->get();

        $slots = [];
        foreach ($availabilities as $availability) {
            $start = Carbon::parse($availability->available_date->format('Y-m-d') . ' ' . $availability->start_time->format('H:i'));
            $end = Carbon::parse($availability->available_date->format('Y-m-d') . ' ' . $availability->end_time->format('H:i'));
            $duration = $availability->slot_duration;
            
            while ($start->copy()->addMinutes($duration) <= $end) {
                $slotEnd = $start->copy()->addMinutes($duration);
                
                // Check if this slot is already booked
                $isBooked = Appointment::where('guidance_associate_id', $availability->guidance_associate_id)
                    ->where('appointment_date', $date)
                    ->where('start_time', $start->format('H:i'))
                    ->where('end_time', $slotEnd->format('H:i'))
                    ->whereHas('status', function ($q) {
                        $q->whereIn('name', ['pending', 'approved']);
                    })
                    ->exists();

                // Check if student already has an appointment at this time
                $studentConflict = false;
                if (Auth::check()) {
                    $studentConflict = Appointment::where('student_id', Auth::id())
                        ->where('appointment_date', $date)
                        ->where('start_time', '<', $slotEnd->format('H:i'))
                        ->where('end_time', '>', $start->format('H:i'))
                        ->whereHas('status', function ($q) {
                            $q->whereIn('name', ['pending', 'approved']);
                        })
                        ->exists();
                }

                $slots[] = [
                    'availability_id' => $availability->id,
                    'guidance_associate_id' => $availability->guidance_associate_id,
                    'guidance_associate_name' => $availability->guidanceAssociate->full_name,
                    'provider_role' => $availability->guidanceAssociate->role->name,
                    'school' => $availability->guidanceAssociate->school,
                    'profile_photo' => $availability->guidanceAssociate->profile_photo
                        ? asset('storage/' . $availability->guidanceAssociate->profile_photo)
                        : null,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'formatted_time' => $start->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'is_booked' => $isBooked,
                    'student_conflict' => $studentConflict,
                ];
                
                $start = $slotEnd;
            }
        }

        return response()->json($slots);
    }

    private function scheduleProvidersFor(User $student)
    {
        return User::where('status', 'active')
            ->select(['id', 'role_id', 'school', 'first_name', 'middle_name', 'last_name', 'profile_photo'])
            ->whereHas('role', function ($query) {
                $query->whereIn('name', ['admin', 'guidance_associate']);
            })
            ->with('role')
            ->orderBy('first_name')
            ->get()
            ->filter(function (User $provider) use ($student) {
                if ($provider->role->name === 'admin') {
                    return true;
                }

                return $student->school !== null
                    && $student->school !== ''
                    && $provider->school === $student->school;
            })
            ->map(function (User $provider) {
                return [
                    'id' => $provider->id,
                    'guidance_associate_name' => $provider->full_name,
                    'provider_role' => $provider->role->name,
                    'school' => $provider->school,
                    'profile_photo' => $provider->profile_photo
                        ? asset('storage/' . $provider->profile_photo)
                        : null,
                ];
            })
            ->values();
    }

    private function hasEligibleProvider(Availability $availability, string $eligibleRole, ?string $studentSchool): bool
    {
        $provider = $availability->guidanceAssociate;

        if (!$provider
            || $availability->status !== 'available'
            || $provider->status !== 'active'
            || !$provider->role
            || $provider->role->name !== $eligibleRole) {
            return false;
        }

        if ($eligibleRole === 'guidance_associate') {
            return $studentSchool !== null
                && $studentSchool !== ''
                && $provider->school === $studentSchool;
        }

        return $eligibleRole === 'admin';
    }

    public function index()
    {
        $appointments = Appointment::where('student_id', Auth::id())
            ->with(['guidanceAssociate', 'status'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        $availableDates = Availability::where('status', 'available')
            ->where('available_date', '>=', Carbon::today())
            ->whereHas('guidanceAssociate', function ($query) {
                $query->where('status', 'active')
                    ->whereHas('role', function ($roleQuery) {
                        $roleQuery->whereIn('name', ['admin', 'guidance_associate']);
                    });
            })
            ->select('available_date')
            ->distinct()
            ->orderBy('available_date')
            ->get();

        return view('student.appointments.index', compact('appointments', 'availableDates'));
    }

    public function create()
    {
        return redirect()->route('student.schedules');
    }

    public function store(Request $request)
    {
        $request->validate([
            'availability_id' => 'required|exists:availability,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required',
            'purpose' => 'required|string|max:1000',
            'service_type' => 'required|in:Counseling,Coaching',
            'concern_category' => 'required|string|max:100',
            'appointment_mode' => 'required|in:online,in_person',
        ]);

        $categories = [
            'Counseling' => [
                'Personal Concerns', 'Family Concerns', 'Peer / Social Concerns',
                'Emotional / Well-being Concerns', 'Career / Educational Planning',
                'Adjustment Concerns', 'Behavioral Concerns', 'Other',
            ],
            'Coaching' => [
                'Academic Concerns', 'Financial Concerns', 'Career / Educational Planning',
            ],
        ];
        $serviceType = $request->input('service_type');
        $concern = $request->input('concern_category');
        $request->validate([
            'concern_category' => ['required', 'string', Rule::in($categories[$serviceType])],
        ]);
        $eligibleRole = in_array($concern, ['Academic Concerns', 'Financial Concerns', 'Career / Educational Planning'], true)
            ? 'guidance_associate' : 'admin';

        $severity = $request->input('severity', 'not_assessed');
        $studentSchool = Auth::user()->school;

        $availability = Availability::with('guidanceAssociate.role')->findOrFail($request->availability_id);
        if (!$this->hasEligibleProvider($availability, $eligibleRole, $studentSchool)) {
            throw ValidationException::withMessages([
                'availability_id' => 'The selected time slot is not available for this service and concern.',
            ]);
        }

        // Validate that the submitted date matches the availability record
        if ($request->appointment_date !== $availability->available_date->format('Y-m-d')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The selected date does not match the schedule.'], 422);
            }
            return redirect()->route('student.schedules')->withErrors(['slot' => 'The selected date does not match the schedule.']);
        }

        // Validate that the submitted time falls within the availability record's time range
        $submittedStart = \Carbon\Carbon::parse($request->start_time);
        $submittedEnd = \Carbon\Carbon::parse($request->end_time);
        $availsStart = $availability->start_time;
        $availsEnd = $availability->end_time;
        $duration = $availability->slot_duration;

        // Check that the submitted time is within the availability range
        if ($submittedStart->format('H:i') < $availsStart->format('H:i') || $submittedEnd->format('H:i') > $availsEnd->format('H:i')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The selected time is outside the schedule\'s available hours.'], 422);
            }
            return redirect()->route('student.schedules')->withErrors(['slot' => 'The selected time is outside the schedule\'s available hours.']);
        }

        // Check that the submitted time aligns with the slot duration
        $slotMinutes = $submittedStart->diffInMinutes($submittedEnd);
        $expectedMinutes = $duration;
        $slotOffset = $availsStart->diffInMinutes($submittedStart);
        if ($slotMinutes != $expectedMinutes || $slotOffset % $duration !== 0 || $submittedEnd->format('H:i') <= $submittedStart->format('H:i')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The selected time slot duration does not match the schedule.'], 422);
            }
            return redirect()->route('student.schedules')->withErrors(['slot' => 'The selected time slot duration does not match the schedule.']);
        }

        $appointment = null;
        $errorMessage = null;

        try {
            $appointment = DB::transaction(function () use ($request, $availability, $severity, $serviceType, $concern, $eligibleRole, $studentSchool, &$errorMessage) {
                // Lock the availability row for update to prevent race conditions
                $availability = Availability::with('guidanceAssociate.role')
                    ->where('id', $availability->id)
                    ->lockForUpdate()
                    ->first();

                if (!$availability
                    || !$this->hasEligibleProvider($availability, $eligibleRole, $studentSchool)
                    || $availability->available_date->format('Y-m-d') !== $request->appointment_date) {
                    $errorMessage = 'The selected time slot is no longer available for this service and concern.';
                    return null;
                }

                // Double-check slot is still available (inside transaction with lock)
                $existing = Appointment::where('guidance_associate_id', $availability->guidance_associate_id)
                    ->where('appointment_date', $request->appointment_date)
                    ->where('start_time', $request->start_time)
                    ->where('end_time', $request->end_time)
                    ->whereHas('status', function ($q) {
                        $q->whereIn('name', ['pending', 'approved']);
                    })
                    ->exists();

                if ($existing) {
                    $errorMessage = 'This time slot is no longer available. Please select another time.';
                    return null;
                }

                // Check student conflict
                $studentConflict = Appointment::where('student_id', Auth::id())
                    ->where('appointment_date', $request->appointment_date)
                    ->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time)
                    ->whereHas('status', function ($q) {
                        $q->whereIn('name', ['pending', 'approved']);
                    })
                    ->exists();

                if ($studentConflict) {
                    $errorMessage = 'You already have an appointment at this time.';
                    return null;
                }

                $pendingStatus = AppointmentStatus::where('name', 'pending')->first();

                $appointment = Appointment::create([
                    'student_id' => Auth::id(),
                    'guidance_associate_id' => $availability->guidance_associate_id,
                    'appointment_status_id' => $pendingStatus->id,
                    'appointment_date' => $request->appointment_date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'purpose' => $request->purpose,
                    'service_type' => $serviceType,
                    'concern_category' => $concern,
                    'appointment_mode' => $request->appointment_mode,
                    'severity' => $severity,
                ]);

                // Handle notifications based on severity
                if ($severity === 'high') {
                    // Notify admins only
                    $admins = User::whereHas('role', function ($q) {
                        $q->where('name', 'admin');
                    })->get();

                    foreach ($admins as $admin) {
                        Notification::create([
                            'user_id' => $admin->id,
                            'title' => 'High Severity Appointment Request',
                            'message' => "A student has submitted a HIGH SEVERITY guidance appointment request for {$request->appointment_date} at {$request->start_time}. Requires admin review.",
                            'type' => 'appointment_request_high',
                            'related_appointment_id' => $appointment->id,
                        ]);
                    }
                } else {
                    // Notify guidance associate normally
                    Notification::create([
                        'user_id' => $availability->guidance_associate_id,
                        'title' => 'New Appointment Request',
                        'message' => "A student has submitted a new guidance appointment request for {$request->appointment_date} at {$request->start_time}.",
                        'type' => 'appointment_request',
                        'related_appointment_id' => $appointment->id,
                    ]);
                }

                // Notify student
                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => 'Appointment Request Submitted',
                    'message' => "Your guidance appointment request has been submitted for {$request->appointment_date} at {$request->start_time}.",
                    'type' => 'appointment_request',
                    'related_appointment_id' => $appointment->id,
                ]);

                ActivityLog::log('CREATE_APPOINTMENT', "Created appointment #{$appointment->id} for student " . Auth::user()->full_name, 'Appointments', Auth::id());

                return $appointment;
            });
        } catch (\Exception $e) {
            report($e);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'An error occurred while booking. Please try again.'], 500);
            }
            return redirect()->route('student.schedules')->withErrors(['slot' => 'An error occurred while booking. Please try again.']);
        }

        if ($errorMessage) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 409);
            }
            return redirect()->route('student.schedules')->withErrors(['slot' => $errorMessage]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $severity === 'high'
                    ? 'High severity appointment request submitted! Admins have been notified and will review your request.'
                    : 'Appointment request submitted successfully!',
                'appointment' => [
                    'id' => $appointment->id,
                    'date' => $appointment->formatted_date,
                    'time' => $appointment->formatted_time,
                    'guidance_associate' => $appointment->guidanceAssociate->full_name,
                ],
            ], 201);
        }

        if ($severity === 'high') {
            return redirect()->route('student.appointments.index')->with('success', 'High severity appointment request submitted! Admins have been notified and will review your request.');
        }

        return redirect()->route('student.appointments.index')->with('success', 'Appointment request submitted successfully!');
    }

    public function show(Appointment $appointment)
    {
        $this->authorizeStudent($appointment);
        
        $appointment->load(['guidanceAssociate.role', 'status', 'feedback', 'rescheduleRequests']);
        
        $availableDates = Availability::where('status', 'available')
            ->where('available_date', '>=', Carbon::today())
            ->whereHas('guidanceAssociate', function ($query) {
                $query->where('status', 'active');
            })
            ->select('available_date')
            ->distinct()
            ->orderBy('available_date')
            ->get();

        return view('student.appointments.show', compact('appointment', 'availableDates'));
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $this->authorizeStudent($appointment);
        
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        if (!$appointment->canBeCancelled()) {
            return back()->withErrors(['error' => 'This appointment cannot be cancelled.']);
        }

        DB::transaction(function () use ($appointment, $request) {
            $cancelledStatus = AppointmentStatus::where('name', 'cancelled')->first();
            
            $appointment->update([
                'appointment_status_id' => $cancelledStatus->id,
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
            ]);

            // Notify guidance associate
            Notification::create([
                'user_id' => $appointment->guidance_associate_id,
                'title' => 'Appointment Cancelled',
                'message' => "An appointment has been cancelled by the student.",
                'type' => 'appointment_cancelled',
                'related_appointment_id' => $appointment->id,
            ]);

            // Notify student
            Notification::create([
                'user_id' => $appointment->student_id,
                'title' => 'Appointment Cancelled',
                'message' => "Your appointment has been cancelled.",
                'type' => 'appointment_cancelled',
                'related_appointment_id' => $appointment->id,
            ]);

            ActivityLog::log('CANCEL_APPOINTMENT', "Cancelled appointment #{$appointment->id}", 'Appointments', Auth::id());
        });

        return redirect()->route('student.appointments.index')->with('success', 'Appointment cancelled successfully.');
    }

    public function rescheduleForm(Appointment $appointment)
    {
        $this->authorizeStudent($appointment);
        
        if (!$appointment->canBeRescheduled()) {
            return back()->withErrors(['error' => 'This appointment cannot be rescheduled.']);
        }

        $availableDates = Availability::where('status', 'available')
            ->where('available_date', '>=', Carbon::today())
            ->whereHas('guidanceAssociate', function ($query) {
                $query->where('status', 'active');
            })
            ->select('available_date')
            ->distinct()
            ->orderBy('available_date')
            ->get();

        return view('student.appointments.reschedule', compact('appointment', 'availableDates'));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $this->authorizeStudent($appointment);
        
        $request->validate([
            'availability_id' => 'required|exists:availability,id',
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_start_time' => 'required',
            'requested_end_time' => 'required',
            'reason' => 'required|string|max:1000',
        ]);

        $availability = Availability::with('guidanceAssociate')->findOrFail($request->availability_id);
        
        DB::transaction(function () use ($request, $appointment, $availability) {
            $rescheduleStatus = AppointmentStatus::where('name', 'reschedule_requested')->first();
            
            $appointment->update([
                'appointment_status_id' => $rescheduleStatus->id,
                'reschedule_reason' => $request->reason,
            ]);

            \App\Models\RescheduleRequest::create([
                'appointment_id' => $appointment->id,
                'requested_by' => Auth::id(),
                'old_date' => $appointment->appointment_date,
                'old_start_time' => $appointment->start_time,
                'old_end_time' => $appointment->end_time,
                'requested_date' => $request->requested_date,
                'requested_start_time' => $request->requested_start_time,
                'requested_end_time' => $request->requested_end_time,
                'reason' => $request->reason,
            ]);

            // Notify guidance associate
            Notification::create([
                'user_id' => $appointment->guidance_associate_id,
                'title' => 'Reschedule Request',
                'message' => "A student has requested to reschedule their appointment.",
                'type' => 'appointment_rescheduled',
                'related_appointment_id' => $appointment->id,
            ]);

            ActivityLog::log('RESCHEDULE_REQUEST', "Requested reschedule for appointment #{$appointment->id}", 'Appointments', Auth::id());
        });

        return redirect()->route('student.appointments.index')->with('success', 'Reschedule request submitted successfully!');
    }

    private function authorizeStudent(Appointment $appointment)
    {
        if ($appointment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}