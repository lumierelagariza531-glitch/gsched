<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $availabilities = Availability::where('guidance_associate_id', $user->id)
            ->where('available_date', '>=', Carbon::today())
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get();

        $appointments = Appointment::where('guidance_associate_id', $user->id)
            ->where('appointment_date', '>=', Carbon::today())
            ->with(['student', 'status'])
            ->orderBy('appointment_date')
            ->get()
            ->groupBy('appointment_date');

        $availabilityMap = [];
        foreach ($availabilities as $availability) {
            $dateStr = $availability->available_date->format('Y-m-d');
            $start = Carbon::parse($dateStr . ' ' . $availability->start_time->format('H:i'));
            $end = Carbon::parse($dateStr . ' ' . $availability->end_time->format('H:i'));
            $duration = $availability->slot_duration;

            $totalSlots = 0;
            $bookedSlots = 0;
            $current = $start->copy();
            while ($current->copy()->addMinutes($duration) <= $end) {
                $slotEnd = $current->copy()->addMinutes($duration);
                $totalSlots++;

                $isBooked = Appointment::where('guidance_associate_id', $availability->guidance_associate_id)
                    ->where('appointment_date', $dateStr)
                    ->where('start_time', $current->format('H:i'))
                    ->where('end_time', $slotEnd->format('H:i'))
                    ->whereHas('status', function ($q) {
                        $q->whereIn('name', ['pending', 'approved']);
                    })
                    ->exists();

                if ($isBooked) {
                    $bookedSlots++;
                }

                $current = $slotEnd;
            }

            $availabilityMap[$dateStr][] = [
                'availability_id' => $availability->id,
                'start_time' => $availability->start_time->format('g:i A'),
                'end_time' => $availability->end_time->format('g:i A'),
                'slot_duration' => $availability->slot_duration,
                'status' => $availability->status,
                'is_booked' => $availability->isBooked(),
                'total_slots' => $totalSlots,
                'available_slots' => $totalSlots - $bookedSlots,
                'booked_slots' => $bookedSlots,
            ];
        }

        $appointmentsMap = [];
        foreach ($appointments as $date => $apps) {
            $dateStr = Carbon::parse($date)->format('Y-m-d');
            $appointmentsMap[$dateStr] = [];
            foreach ($apps as $appt) {
                $appointmentsMap[$dateStr][] = [
                    'id' => $appt->id,
                    'student_name' => $appt->student ? $appt->student->full_name : 'Unknown',
                    'time' => $appt->start_time->format('g:i A') . ' - ' . $appt->end_time->format('g:i A'),
                    'status' => $appt->status->name ?? '',
                    'status_label' => $appt->status->label ?? '',
                    'purpose' => $appt->purpose,
                    'notes' => $appt->notes,
                ];
            }
        }

        return view('guidance.calendar', compact('availabilityMap', 'appointmentsMap'));
    }
}