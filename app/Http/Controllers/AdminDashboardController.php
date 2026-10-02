<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalStudents = User::whereHas('role', function ($q) { $q->where('name', 'student'); })->count();
        $totalGuidance = User::whereHas('role', function ($q) { $q->where('name', 'guidance_associate'); })->count();
        
        $totalAppointments = Appointment::count();
        
        $pendingStatus = AppointmentStatus::where('name', 'pending')->first();
        $approvedStatus = AppointmentStatus::where('name', 'approved')->first();
        $completedStatus = AppointmentStatus::where('name', 'completed')->first();
        $cancelledStatus = AppointmentStatus::where('name', 'cancelled')->first();
        
        $pendingAppointments = $pendingStatus ? Appointment::where('appointment_status_id', $pendingStatus->id)->count() : 0;
        $approvedAppointments = $approvedStatus ? Appointment::where('appointment_status_id', $approvedStatus->id)->count() : 0;
        $completedAppointments = $completedStatus ? Appointment::where('appointment_status_id', $completedStatus->id)->count() : 0;
        $cancelledAppointments = $cancelledStatus ? Appointment::where('appointment_status_id', $cancelledStatus->id)->count() : 0;

        // Monthly appointments for chart
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = Appointment::whereYear('appointment_date', $date->year)
                ->whereMonth('appointment_date', $date->month)
                ->count();
        }

        // Status distribution
        $statusData = [];
        $statusLabels = [];
        $statusColors = [];
        $statusColorMap = [
            'pending' => '#FACC15',
            'approved' => '#3B82F6',
            'rejected' => '#EF4444',
            'reschedule_requested' => '#F97316',
            'rescheduled' => '#8B5CF6',
            'cancelled' => '#EF4444',
            'completed' => '#22C55E',
            'no_show' => '#6B7280',
        ];
        $statuses = AppointmentStatus::orderBy('sort_order')->get();
        foreach ($statuses as $status) {
            $statusLabels[] = $status->label;
            $statusData[] = Appointment::where('appointment_status_id', $status->id)->count();
            $statusColors[] = $statusColorMap[strtolower($status->name)] ?? '#6B7280';
        }

        // Appointment Volume by School
        $schoolLabels = User::getSchoolOptions();
        $schoolData = [];
        foreach ($schoolLabels as $school) {
            $schoolData[$school] = Appointment::whereHas('student', function ($q) use ($school) {
                $q->where('school', $school);
            })->count();
        }

        // Age Range Distribution
        $ageRanges = ['15-17' => 0, '18-20' => 0, '21-23' => 0, '24-26' => 0, '27+' => 0];
        $students = User::whereHas('role', function ($q) { $q->where('name', 'student'); })->get();
        foreach ($students as $student) {
            $age = $student->age;
            if ($age === null) continue;
            if ($age >= 15 && $age <= 17) $ageRanges['15-17']++;
            elseif ($age >= 18 && $age <= 20) $ageRanges['18-20']++;
            elseif ($age >= 21 && $age <= 23) $ageRanges['21-23']++;
            elseif ($age >= 24 && $age <= 26) $ageRanges['24-26']++;
            elseif ($age >= 27) $ageRanges['27+']++;
        }
        $ageRangeLabels = array_keys($ageRanges);
        $ageRangeData = array_values($ageRanges);

        // Gender Distribution
        $genderLabels = [];
        $genderData = [];
        $genderCounts = User::whereHas('role', function ($q) { $q->where('name', 'student'); })
            ->whereNotNull('gender')
            ->selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();
        $genderMap = User::getGenderOptions();
        foreach ($genderMap as $value => $label) {
            $genderLabels[] = $label;
            $genderData[] = $genderCounts[$value] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalGuidance',
            'totalAppointments',
            'pendingAppointments',
            'approvedAppointments',
            'completedAppointments',
            'cancelledAppointments',
            'monthlyLabels',
            'monthlyData',
            'statusLabels',
            'statusData',
            'statusColors',
            'schoolLabels',
            'schoolData',
            'ageRangeLabels',
            'ageRangeData',
            'genderLabels',
            'genderData'
        ));
    }

    public function appointments()
    {
        $appointments = Appointment::with(['student', 'guidanceAssociate', 'status'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('admin.appointments', compact('appointments'));
    }

    public function showAppointment(Appointment $appointment)
    {
        $appointment->load(['student', 'guidanceAssociate.role', 'status', 'feedback', 'rescheduleRequests']);
        
        return view('admin.appointments.show', compact('appointment'));
    }
}