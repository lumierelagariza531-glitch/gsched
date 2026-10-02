<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AppointmentWorkflowController extends Controller
{
    public function approve(Request $request, Appointment $appointment, AppointmentWorkflowService $workflow)
    {
        $this->authorizeStaff($appointment);

        $workflow->approve($appointment, Auth::user());
        $isAdmin = Auth::user()->isAdmin();
        $destination = $isAdmin
            ? route('admin.appointments.show', $appointment)
            : route('guidance.requests');

        return redirect($destination)->with('success', 'Appointment approved successfully!');
    }

    public function updateMeetingUrl(Request $request, Appointment $appointment, AppointmentWorkflowService $workflow)
    {
        $this->authorizeMeetingLinkEditor($appointment);

        $validated = $request->validate([
            'online_meeting_url' => [
                'required',
                'string',
                'max:2048',
                'url',
                function ($attribute, $value, $fail) {
                    if (!in_array(strtolower(parse_url($value, PHP_URL_SCHEME) ?: ''), ['http', 'https'], true)) {
                        $fail('The meeting link must use HTTP or HTTPS.');
                    }
                },
            ],
        ]);

        $changed = $workflow->updateMeetingUrl($appointment, Auth::user(), $validated['online_meeting_url']);

        return back()->with(
            'success',
            $changed ? 'Online meeting link updated and the student was notified.' : 'The online meeting link is unchanged.'
        );
    }

    public function complete(Appointment $appointment, AppointmentWorkflowService $workflow)
    {
        $workflow->complete($appointment, Auth::user());

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'Counseling session marked as completed. The student has been prompted to provide feedback.');
    }

    private function authorizeStaff(Appointment $appointment): void
    {
        $user = Auth::user();
        $canApprove = $user && (
            $user->isAdmin()
            || ($user->isGuidanceAssociate() && $appointment->guidance_associate_id === $user->id)
        );

        abort_unless($canApprove, 403, 'Unauthorized access.');
    }

    private function authorizeMeetingLinkEditor(Appointment $appointment): void
    {
        $user = Auth::user();
        $canEdit = $user && (
            $user->isAdmin()
            || ($user->isGuidanceAssociate() && $appointment->guidance_associate_id === $user->id)
        );

        abort_unless($canEdit, 403, 'Unauthorized access.');
    }
}
