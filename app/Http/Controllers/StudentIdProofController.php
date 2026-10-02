<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentIdProofController extends Controller
{
    public function showForUser(User $user, string $side)
    {
        abort_unless($user->isStudent(), 404);

        $viewer = Auth::user();
        abort_unless(
            ($viewer->isStudent() && $viewer->id === $user->id) || $viewer->isAdmin(),
            403
        );

        return $this->imageResponse($user, $side);
    }

    public function showForAppointment(Appointment $appointment, string $side)
    {
        $viewer = Auth::user();
        abort_unless($viewer->isGuidanceAssociate(), 403);
        abort_unless($appointment->guidance_associate_id === $viewer->id, 403);
        abort_if($appointment->isHighSeverity(), 404);

        $student = $appointment->student;
        abort_unless($student && $student->isStudent(), 404);

        return $this->imageResponse($student, $side);
    }

    private function imageResponse(User $student, string $side)
    {
        abort_unless(in_array($side, ['front', 'back'], true), 404);

        $path = $student->{"student_id_{$side}"};
        $safePath = '#\Astudent-id-proofs/'.$student->id.'/'.$side.'\.(?:jpg|jpeg|png|webp)\z#D';
        abort_unless(is_string($path) && preg_match($safePath, $path) === 1, 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);

        $mimeType = $disk->mimeType($path);
        abort_unless(in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        return response($disk->get($path), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "inline; filename=\"student-id-{$side}\"",
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
