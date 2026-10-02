<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentWorkflowService
{
    public function approve(Appointment $appointment, User $actor): Appointment
    {
        return DB::transaction(function () use ($appointment, $actor) {
            $lockedAppointment = Appointment::with(['status', 'student'])
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$lockedAppointment->isPending()) {
                throw ValidationException::withMessages([
                    'appointment' => 'This appointment cannot be approved.',
                ]);
            }

            $facebookProfileUrl = $lockedAppointment->appointment_mode === 'online'
                ? $actor->safe_facebook_profile_url
                : null;
            if ($lockedAppointment->appointment_mode === 'online' && !$facebookProfileUrl) {
                throw ValidationException::withMessages([
                    'facebook_profile_url' => 'Add a valid Facebook profile URL in your profile before approving an online appointment.',
                ]);
            }

            $approvedStatus = AppointmentStatus::where('name', 'approved')->firstOrFail();
            $lockedAppointment->update([
                'appointment_status_id' => $approvedStatus->id,
                'approved_at' => now(),
                'online_meeting_url' => null,
            ]);

            $message = "Your guidance appointment has been approved for {$lockedAppointment->formatted_date} at {$lockedAppointment->formatted_time}.";
            if ($onlineMeetingUrl = $lockedAppointment->safe_online_meeting_url) {
                $message .= " Join online: {$onlineMeetingUrl}";
            }
            if ($facebookProfileUrl) {
                $message .= " Contact your counselor on Facebook: {$facebookProfileUrl}";
            }

            Notification::create([
                'user_id' => $lockedAppointment->student_id,
                'title' => 'Appointment Approved',
                'message' => $message,
                'type' => 'appointment_approved',
                'related_appointment_id' => $lockedAppointment->id,
                'facebook_profile_url' => $facebookProfileUrl,
            ]);

            ActivityLog::log(
                'APPROVE_APPOINTMENT',
                "Approved appointment #{$lockedAppointment->id} for student {$lockedAppointment->student->full_name}",
                'Appointments',
                $actor->id
            );

            return $lockedAppointment->fresh(['status', 'student']);
        });
    }

    public function updateMeetingUrl(Appointment $appointment, User $actor, string $meetingUrl): bool
    {
        return DB::transaction(function () use ($appointment, $actor, $meetingUrl) {
            $lockedAppointment = Appointment::with('status')
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$lockedAppointment->isApproved() || $lockedAppointment->appointment_mode !== 'online') {
                throw ValidationException::withMessages([
                    'appointment' => 'Meeting links can only be updated for approved online appointments.',
                ]);
            }

            if (!$this->isHttpUrl($meetingUrl)) {
                throw ValidationException::withMessages([
                    'online_meeting_url' => 'Enter a valid HTTP or HTTPS meeting link.',
                ]);
            }

            if ($lockedAppointment->online_meeting_url === $meetingUrl) {
                return false;
            }

            $lockedAppointment->update(['online_meeting_url' => $meetingUrl]);

            $onlineMeetingUrl = $lockedAppointment->safe_online_meeting_url;

            Notification::create([
                'user_id' => $lockedAppointment->student_id,
                'title' => 'Online Meeting Link Updated',
                'message' => "The online meeting link for your guidance appointment on {$lockedAppointment->formatted_date} at {$lockedAppointment->formatted_time} has been added or updated. Join online: {$onlineMeetingUrl}",
                'type' => 'appointment_meeting_link',
                'related_appointment_id' => $lockedAppointment->id,
            ]);

            ActivityLog::log(
                'UPDATE_APPOINTMENT_MEETING_LINK',
                "Updated online meeting link for appointment #{$lockedAppointment->id}",
                'Appointments',
                $actor->id
            );

            return true;
        });
    }

    public function complete(Appointment $appointment, User $actor): Appointment
    {
        return DB::transaction(function () use ($appointment, $actor) {
            $lockedAppointment = Appointment::with(['status', 'student'])
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$lockedAppointment->isApproved()) {
                throw ValidationException::withMessages([
                    'error' => 'Only approved appointments can be marked as completed.',
                ]);
            }

            $completedStatus = AppointmentStatus::where('name', 'completed')->firstOrFail();
            $lockedAppointment->update([
                'appointment_status_id' => $completedStatus->id,
                'completed_at' => now(),
            ]);

            Notification::create([
                'user_id' => $lockedAppointment->student_id,
                'title' => 'Appointment Completed',
                'message' => 'Your guidance appointment has been marked as completed. Please provide feedback.',
                'type' => 'feedback',
                'related_appointment_id' => $lockedAppointment->id,
            ]);

            ActivityLog::log(
                'COMPLETE_APPOINTMENT',
                "Completed appointment #{$lockedAppointment->id} for student {$lockedAppointment->student->full_name}",
                'Appointments',
                $actor->id
            );

            return $lockedAppointment->fresh(['status', 'student']);
        });
    }

    private function isHttpUrl(?string $url): bool
    {
        $scheme = is_string($url) ? strtolower(parse_url($url, PHP_URL_SCHEME) ?: '') : '';

        return in_array($scheme, ['http', 'https'], true)
            && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
