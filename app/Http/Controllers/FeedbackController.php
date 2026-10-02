<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function studentIndex()
    {
        $completedStatus = AppointmentStatus::where('name', 'completed')->first();
        
        $appointments = Appointment::where('student_id', Auth::id())
            ->where('appointment_status_id', $completedStatus->id)
            ->whereDoesntHave('feedback')
            ->with('guidanceAssociate')
            ->orderBy('appointment_date', 'desc')
            ->get();

        $submittedFeedback = Feedback::where('student_id', Auth::id())
            ->with('appointment.guidanceAssociate')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('student.feedback.index', compact('appointments', 'submittedFeedback'));
    }

    public function create(Appointment $appointment)
    {
        if ($appointment->student_id !== Auth::id()) {
            abort(403);
        }

        $completedStatus = AppointmentStatus::where('name', 'completed')->first();
        
        if ($appointment->appointment_status_id !== $completedStatus->id) {
            return redirect()->route('student.feedback.index')
                ->withErrors(['error' => 'Feedback can only be submitted for completed appointments.']);
        }

        if ($appointment->feedback) {
            return redirect()->route('student.feedback.index')
                ->withErrors(['error' => 'Feedback has already been submitted for this appointment.']);
        }

        return view('student.feedback.create', compact('appointment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'sqd0' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd1' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd2' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd3' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd4' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd5' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd6' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd7' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'sqd8' => 'required|string|in:' . implode(',', array_keys(\App\Models\Feedback::SQD_OPTIONS)),
            'suggestions' => 'nullable|string|max:2000',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        
        if ($appointment->student_id !== Auth::id()) {
            abort(403);
        }

        $completedStatus = AppointmentStatus::where('name', 'completed')->first();
        
        if ($appointment->appointment_status_id !== $completedStatus->id) {
            return back()->withErrors(['error' => 'Feedback can only be submitted for completed appointments.']);
        }

        if ($appointment->feedback) {
            return back()->withErrors(['error' => 'Feedback has already been submitted for this appointment.']);
        }

        $sqdFields = array_keys(Feedback::SQD_QUESTIONS);
        $answers = $request->only($sqdFields);
        $rating = Feedback::calculateRating($answers);

        DB::transaction(function () use ($request, $appointment, $sqdFields, $answers, $rating) {
            $feedbackData = [
                'appointment_id' => $appointment->id,
                'student_id' => Auth::id(),
                'rating' => $rating,
                'submitted_at' => now(),
            ];

            foreach ($sqdFields as $sqd) {
                $feedbackData[$sqd] = $answers[$sqd];
            }

            $feedbackData['suggestions'] = $request->suggestions;

            Feedback::create($feedbackData);

            // Notify guidance associate
            Notification::create([
                'user_id' => $appointment->guidance_associate_id,
                'title' => 'New Feedback Received',
                'message' => "A student has submitted feedback for your appointment on {$appointment->formatted_date}.",
                'type' => 'feedback',
                'related_appointment_id' => $appointment->id,
            ]);

            ActivityLog::log('SUBMIT_FEEDBACK', "Submitted feedback for appointment #{$appointment->id}", 'Feedback', Auth::id());
        });

        return redirect()->route('student.feedback.index')->with('success', 'Thank you! Your feedback has been submitted successfully.');
    }
}