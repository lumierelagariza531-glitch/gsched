<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GuidanceDashboardController extends Controller
{
public function index()
    {
        $user = Auth::user();
        
        $pendingStatus = AppointmentStatus::where('name', 'pending')->first();
        $approvedStatus = AppointmentStatus::where('name', 'approved')->first();
        $cancelledStatus = AppointmentStatus::where('name', 'cancelled')->first();
        $completedStatus = AppointmentStatus::where('name', 'completed')->first();
        
        $pendingRequests = Appointment::where('guidance_associate_id', $user->id)
            ->where('appointment_status_id', $pendingStatus->id)
            ->with('student')
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();
            
        $todaysAppointments = Appointment::where('appointment_date', Carbon::today())
            ->where('guidance_associate_id', $user->id)
            ->whereHas('status', function ($q) {
                $q->whereIn('name', ['approved', 'pending']);
            })
            ->with('student')
            ->orderBy('start_time')
            ->get();
            
        $upcomingAppointments = Appointment::where('appointment_date', '>', Carbon::today())
            ->where('guidance_associate_id', $user->id)
            ->whereHas('status', function ($q) {
                $q->whereIn('name', ['approved']);
            })
            ->with('student')
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();
            
        $completedCount = Appointment::where('guidance_associate_id', $user->id)
            ->where('appointment_status_id', $completedStatus->id)
            ->count();
        $cancelledCount = Appointment::where('guidance_associate_id', $user->id)
            ->where('appointment_status_id', $cancelledStatus->id)
            ->count();

        $availableSlots = Availability::where('status', 'available')
            ->where('guidance_associate_id', $user->id)
            ->where('available_date', '>=', Carbon::today())
            ->count();

        return view('guidance.dashboard', compact(
            'pendingRequests',
            'todaysAppointments',
            'upcomingAppointments',
            'completedCount',
            'cancelledCount',
            'availableSlots'
        ));
    }
}