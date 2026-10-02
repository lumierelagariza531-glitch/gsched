<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function appointments(Request $request)
    {
        $query = Appointment::with(['student', 'guidanceAssociate', 'status']);

        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }
        if ($request->filled('guidance_associate_id')) {
            $query->where('guidance_associate_id', $request->guidance_associate_id);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(20);
        $statuses = AppointmentStatus::all();
        $guidanceAssociates = User::whereHas('role', fn($q) => $q->where('name', 'guidance_associate'))->get();
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))->get();

        return view('admin.reports.appointments', compact('appointments', 'statuses', 'guidanceAssociates', 'students'));
    }

    public function students(Request $request)
    {
        $query = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->withCount(['studentAppointments' => function ($q) use ($request) {
                if ($request->filled('date_from')) $q->where('appointment_date', '>=', $request->date_from);
                if ($request->filled('date_to')) $q->where('appointment_date', '<=', $request->date_to);
            }])
            ->with(['studentAppointments' => function ($q) use ($request) {
                if ($request->filled('date_from')) $q->where('appointment_date', '>=', $request->date_from);
                if ($request->filled('date_to')) $q->where('appointment_date', '<=', $request->date_to);
            }]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.students', compact('students'));
    }

    public function guidanceAssociates(Request $request)
    {
        $query = User::whereHas('role', fn($q) => $q->where('name', 'guidance_associate'))
            ->withCount(['guidanceAppointments' => function ($q) use ($request) {
                if ($request->filled('date_from')) $q->where('appointment_date', '>=', $request->date_from);
                if ($request->filled('date_to')) $q->where('appointment_date', '<=', $request->date_to);
            }]);

        $guidanceAssociates = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.guidance-associates', compact('guidanceAssociates'));
    }

    public function status(Request $request)
    {
        $statuses = AppointmentStatus::withCount(['appointments' => function ($q) use ($request) {
            if ($request->filled('date_from')) $q->where('appointment_date', '>=', $request->date_from);
            if ($request->filled('date_to')) $q->where('appointment_date', '<=', $request->date_to);
        }])->orderBy('sort_order')->get();

        return view('admin.reports.status', compact('statuses'));
    }

    public function monthly(Request $request)
    {
        $months = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $data[] = Appointment::whereYear('appointment_date', $date->year)
                ->whereMonth('appointment_date', $date->month)
                ->count();
        }

        return view('admin.reports.monthly', compact('months', 'data'));
    }

    public function cancellations(Request $request)
    {
        $query = Appointment::whereHas('status', fn($q) => $q->where('name', 'cancelled'))
            ->with(['student', 'guidanceAssociate']);

        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        $cancellations = $query->orderBy('cancelled_at', 'desc')->paginate(20);

        return view('admin.reports.cancellations', compact('cancellations'));
    }

    public function feedback(Request $request)
    {
        $query = Feedback::with(['appointment.student', 'appointment.guidanceAssociate']);

        if ($request->filled('date_from')) {
            $query->where('submitted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('submitted_at', '<=', $request->date_to);
        }
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $feedback = $query->orderBy('submitted_at', 'desc')->paginate(20);

        $avgRating = Feedback::whereNotNull('rating')->avg('rating');
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = Feedback::where('rating', $i)->count();
        }

        return view('admin.reports.feedback', compact('feedback', 'avgRating', 'ratingDistribution'));
    }

    public function export(Request $request, $type)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $filename = "g-sched_{$type}_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type, $request) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'appointments':
                    fputcsv($file, ['ID', 'Student', 'Guidance Associate', 'Date', 'Time', 'Purpose', 'Status', 'Created At']);
                    
                    $query = Appointment::with(['student', 'guidanceAssociate', 'status']);
                    if ($request->filled('date_from')) $query->where('appointment_date', '>=', $request->date_from);
                    if ($request->filled('date_to')) $query->where('appointment_date', '<=', $request->date_to);
                    if ($request->filled('status')) $query->whereHas('status', fn($q) => $q->where('name', $request->status));
                    
                    $query->chunk(100, function ($appointments) use ($file) {
                        foreach ($appointments as $appt) {
                            fputcsv($file, [
                                $appt->id,
                                $appt->student->full_name,
                                $appt->guidanceAssociate?->full_name,
                                $appt->formatted_date,
                                $appt->formatted_time,
                                $appt->purpose,
                                $appt->status->label,
                                $appt->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;
                    
                case 'students':
                    fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Total Appointments', 'Status', 'Created At']);
                    
                    $query = User::whereHas('role', fn($q) => $q->where('name', 'student'))
                        ->withCount('studentAppointments');
                    if ($request->filled('search')) {
                        $query->where(function ($q) use ($request) {
                            $q->where('first_name', 'like', "%{$request->search}%")
                              ->orWhere('last_name', 'like', "%{$request->search}%");
                        });
                    }
                    
                    $query->chunk(100, function ($users) use ($file) {
                        foreach ($users as $user) {
                            fputcsv($file, [
                                $user->id,
                                $user->full_name,
                                $user->email,
                                $user->phone,
                                $user->student_appointments_count,
                                $user->status,
                                $user->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;
                    
                case 'feedback':
                    fputcsv($file, ['ID', 'Student', 'Guidance Associate', 'Appointment Date', 'Rating', 'SQD0', 'SQD1', 'SQD2', 'SQD3', 'SQD4', 'SQD5', 'SQD6', 'SQD7', 'SQD8', 'Comments', 'Suggestions', 'Submitted At']);
                    
                    $query = Feedback::with(['student', 'appointment.guidanceAssociate']);
                    if ($request->filled('date_from')) $query->where('submitted_at', '>=', $request->date_from);
                    if ($request->filled('date_to')) $query->where('submitted_at', '<=', $request->date_to);
                    
                    $query->chunk(100, function ($feedbacks) use ($file) {
                        foreach ($feedbacks as $fb) {
                            fputcsv($file, [
                                $fb->id,
                                $fb->student->full_name,
                                $fb->appointment->guidanceAssociate?->full_name,
                                $fb->appointment->formatted_date,
                                $fb->rating,
                                $fb->sqd0,
                                $fb->sqd1,
                                $fb->sqd2,
                                $fb->sqd3,
                                $fb->sqd4,
                                $fb->sqd5,
                                $fb->sqd6,
                                $fb->sqd7,
                                $fb->sqd8,
                                $fb->comments,
                                $fb->suggestions,
                                $fb->submitted_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}