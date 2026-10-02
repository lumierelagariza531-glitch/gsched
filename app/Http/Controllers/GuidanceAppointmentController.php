<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\ActivityLog;
use App\Models\Availability;
use App\Models\Notification;
use App\Models\RescheduleRequest;
use App\Models\User;
use App\Services\AppointmentWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuidanceAppointmentController extends Controller
{
    public function requests()
    {
        $pendingStatus = AppointmentStatus::where('name', 'pending')->first();
        
        $requests = Appointment::where('guidance_associate_id', Auth::id())
            ->where('appointment_status_id', $pendingStatus->id)
            ->with(['student', 'status'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->paginate(15);

        return view('guidance.requests', compact('requests'));
    }

    public function showRequest(Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
        $appointment->load(['student', 'guidanceAssociate', 'status', 'rescheduleRequests']);
        
        $activityLogs = ActivityLog::where('module', 'Appointments')
            ->where('description', 'LIKE', '%' . $appointment->id . '%')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->filter(function (ActivityLog $activityLog) use ($appointment) {
                $appointmentReference = '/\bappointment #' . preg_quote((string) $appointment->id, '/') . '(?!\d)/i';

                return preg_match($appointmentReference, $activityLog->description) === 1;
            })
            ->take(50)
            ->values();

        $completedStatus = AppointmentStatus::where('name', 'completed')->first();
        $pastVisitQuery = $completedStatus
            ? Appointment::where('student_id', $appointment->student_id)
                ->where('guidance_associate_id', Auth::id())
                ->where('appointment_status_id', $completedStatus->id)
                ->where('id', '!=', $appointment->id)
                ->whereNotNull('completed_at')
            : null;

        if ($pastVisitQuery && $appointment->completed_at) {
            $pastVisitQuery->where('completed_at', '<', $appointment->completed_at);
        }

        $pastVisitCount = $pastVisitQuery ? $pastVisitQuery->count() : 0;
        
        return view('guidance.requests.show', compact('appointment', 'activityLogs', 'pastVisitCount'));
    }

    public function reject(Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
        if (!$appointment->isPending()) {
            return back()->withErrors(['error' => 'This appointment cannot be rejected.']);
        }

        DB::transaction(function () use ($appointment) {
            $rejectedStatus = AppointmentStatus::where('name', 'rejected')->first();
            
            $appointment->update([
                'appointment_status_id' => $rejectedStatus->id,
            ]);

            // Notify student
            Notification::create([
                'user_id' => $appointment->student_id,
                'title' => 'Appointment Rejected',
                'message' => "Your guidance appointment request for {$appointment->formatted_date} at {$appointment->formatted_time} has been rejected.",
                'type' => 'appointment_rejected',
                'related_appointment_id' => $appointment->id,
            ]);

            ActivityLog::log('REJECT_APPOINTMENT', "Rejected appointment #{$appointment->id} for student {$appointment->student->full_name}", 'Appointments', Auth::id());
        });

        return redirect()->route('guidance.requests')->with('success', 'Appointment rejected.');
    }

    public function rescheduleForm(Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
        $availableDates = Availability::where('status', 'available')
            ->where('guidance_associate_id', Auth::id())
            ->where('available_date', '>=', Carbon::today())
            ->whereHas('guidanceAssociate', function ($query) {
                $query->where('status', 'active');
            })
            ->select('available_date')
            ->distinct()
            ->orderBy('available_date')
            ->get();

        return view('guidance.appointments.reschedule', compact('appointment', 'availableDates'));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
        $request->validate([
            'availability_id' => 'required|exists:availability,id',
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_start_time' => 'required',
            'requested_end_time' => 'required',
            'reason' => 'required|string|max:1000',
        ]);

        $availability = Availability::with('guidanceAssociate')
            ->whereKey($request->availability_id)
            ->where('guidance_associate_id', Auth::id())
            ->firstOrFail();
        
        DB::transaction(function () use ($request, $appointment, $availability) {
            $oldDate = $appointment->appointment_date;
            $oldStart = $appointment->start_time;
            $oldEnd = $appointment->end_time;

            $appointment->update([
                'appointment_date' => $request->requested_date,
                'start_time' => $request->requested_start_time,
                'end_time' => $request->requested_end_time,
                'guidance_associate_id' => $availability->guidance_associate_id,
                'reschedule_reason' => $request->reason,
            ]);

            // Update any pending reschedule request
            RescheduleRequest::where('appointment_id', $appointment->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

            // Notify student
            Notification::create([
                'user_id' => $appointment->student_id,
                'title' => 'Appointment Rescheduled',
                'message' => "Your appointment has been rescheduled to {$request->requested_date} at {$request->requested_start_time}.",
                'type' => 'appointment_rescheduled',
                'related_appointment_id' => $appointment->id,
            ]);

            ActivityLog::log('RESCHEDULE_APPOINTMENT', "Rescheduled appointment #{$appointment->id} from {$oldDate} to {$request->requested_date}", 'Appointments', Auth::id());
        });

        return redirect()->route('guidance.requests')->with('success', 'Appointment rescheduled successfully!');
    }

    public function cancel(Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
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

            // Notify student
            Notification::create([
                'user_id' => $appointment->student_id,
                'title' => 'Appointment Cancelled',
                'message' => "Your appointment has been cancelled by the guidance associate.",
                'type' => 'appointment_cancelled',
                'related_appointment_id' => $appointment->id,
            ]);

            ActivityLog::log('CANCEL_APPOINTMENT', "Cancelled appointment #{$appointment->id} for student {$appointment->student->full_name}", 'Appointments', Auth::id());
        });

        return redirect()->route('guidance.requests')->with('success', 'Appointment cancelled successfully.');
    }

    public function complete(Appointment $appointment, AppointmentWorkflowService $workflow)
    {
        $this->authorizeGuidance($appointment);

        $workflow->complete($appointment, Auth::user());

        return redirect()->route('guidance.appointments')->with('success', 'Appointment marked as completed!');
    }

    public function assignSeverity(Request $request, Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
        $request->validate([
            'severity' => 'required|in:not_assessed,low,moderate,high',
            'notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($appointment, $request) {
            $oldSeverity = $appointment->severity;
            $newSeverity = $request->severity;
            
            $appointment->update([
                'severity' => $newSeverity,
                'notes' => $request->notes,
            ]);

            ActivityLog::log('ASSIGN_SEVERITY', "Updated severity for appointment #{$appointment->id} from " . ($oldSeverity ?: 'not_assessed') . " to {$newSeverity}", 'Appointments', Auth::id());

            // Send student notification for Low, Moderate, or High severity
            if (in_array($newSeverity, ['low', 'moderate', 'high'])) {
                if ($newSeverity === 'high') {
                    $studentMessage = "Your guidance case has been assessed as requiring further attention. You may now proceed to the Student Center Guidance Associate Office for assistance.";
                } else {
                    $studentMessage = "Your guidance case has been assessed. You may now proceed to the Student Center Guidance Associate Office for your scheduled appointment.";
                }
                
                Notification::create([
                    'user_id' => $appointment->student_id,
                    'title' => 'Guidance Case Assessed',
                    'message' => $studentMessage,
                    'type' => 'case_assessed',
                    'related_appointment_id' => $appointment->id,
                ]);

                // Send admin notification only when severity changes TO High
                if ($newSeverity === 'high' && $oldSeverity !== 'high') {
                    $admins = User::whereHas('role', function ($q) {
                        $q->where('name', 'admin');
                    })->get();

                    foreach ($admins as $admin) {
                        Notification::create([
                            'user_id' => $admin->id,
                            'title' => 'High-Severity Case Requires Attention',
                            'message' => "A high-severity guidance case was identified for appointment #{$appointment->id}. Please review the student's case details.",
                            'type' => 'case_high_severity',
                            'related_appointment_id' => $appointment->id,
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Severity updated successfully!');
    }

    public function sendReminder(Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
        Notification::create([
            'user_id' => $appointment->student_id,
            'title' => 'Appointment Reminder',
            'message' => "Reminder: You have a guidance appointment scheduled for {$appointment->formatted_date} at {$appointment->formatted_time}.",
            'type' => 'appointment_reminder',
            'related_appointment_id' => $appointment->id,
        ]);

        ActivityLog::log('SEND_REMINDER', "Sent reminder for appointment #{$appointment->id}", 'Appointments', Auth::id());

        return back()->with('success', 'Reminder sent to student!');
    }

    public function appointments()
    {
        $appointments = Appointment::where('guidance_associate_id', Auth::id())
            ->whereHas('status', function ($q) {
                $q->whereIn('name', ['approved', 'completed']);
            })
            ->with(['student', 'status'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        return view('guidance.appointments', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        $this->authorizeGuidance($appointment);
        
        $appointment->load(['student', 'guidanceAssociate', 'status', 'feedback']);
        
        return view('guidance.appointments.show', compact('appointment'));
    }

    public function history()
    {
        $appointments = Appointment::where('guidance_associate_id', Auth::id())
            ->with(['student', 'status'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('guidance.history', compact('appointments'));
    }

    private function authorizeGuidance(Appointment $appointment)
    {
        if (!Auth::user()->isGuidanceAssociate() || $appointment->guidance_associate_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}