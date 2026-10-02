<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = Availability::where('guidance_associate_id', Auth::id())
            ->with('guidanceAssociate')
            ->orderBy('available_date', 'desc')
            ->orderBy('start_time')
            ->paginate(15);

        return view('guidance.availability', compact('availabilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'available_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
        ]);

        // Check for overlapping availability
        $overlap = Availability::where('guidance_associate_id', Auth::id())
            ->where('available_date', $request->available_date)
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['overlap' => 'This time range overlaps with existing availability.']);
        }

        DB::transaction(function () use ($request) {
            $availability = Availability::create([
                'guidance_associate_id' => Auth::id(),
                'available_date' => $request->available_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'slot_duration' => $request->slot_duration,
                'status' => 'available',
            ]);

            ActivityLog::log('CREATE_AVAILABILITY', "Created availability for {$request->available_date} from {$request->start_time} to {$request->end_time}", 'Availability', Auth::id());
        });

        return redirect()->route('guidance.availability')->with('success', 'Availability added successfully!');
    }

    public function edit(Availability $availability)
    {
        $this->authorizeAvailability($availability);
        
        return view('guidance.availability.edit', compact('availability'));
    }

    public function update(Request $request, Availability $availability)
    {
        $this->authorizeAvailability($availability);
        
        $request->validate([
            'available_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
        ]);

        // Check for overlapping availability (excluding current)
        $overlap = Availability::where('guidance_associate_id', Auth::id())
            ->where('id', '!=', $availability->id)
            ->where('available_date', $request->available_date)
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['overlap' => 'This time range overlaps with existing availability.']);
        }

        // If status is booked, don't allow changing date/time
        if ($availability->status === 'booked') {
            return back()->withErrors(['error' => 'Cannot modify a booked slot.']);
        }

        $availability->update([
            'available_date' => $request->available_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'slot_duration' => $request->slot_duration,
        ]);

        ActivityLog::log('UPDATE_AVAILABILITY', "Updated availability #{$availability->id}", 'Availability', Auth::id());

        return redirect()->route('guidance.availability')->with('success', 'Availability updated successfully!');
    }

    public function destroy(Request $request, Availability $availability)
    {
        $this->authorizeAvailability($availability);
        
        if ($availability->status === 'booked') {
            return back()->withErrors(['error' => 'Cannot delete a booked slot.']);
        }

        $availability->delete();

        ActivityLog::log('DELETE_AVAILABILITY', "Deleted availability #{$availability->id}", 'Availability', Auth::id());

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Availability deleted successfully!']);
        }

        return redirect()->route('guidance.availability')->with('success', 'Availability deleted successfully!');
    }

    private function authorizeAvailability(Availability $availability)
    {
        if ($availability->guidance_associate_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
    }

    // Admin methods
    public function adminIndex()
    {
        $availabilities = Availability::whereHas('guidanceAssociate', function ($q) {
            $q->where('status', 'active')->whereHas('role', function ($role) {
                $role->whereIn('name', ['guidance_associate', 'admin']);
            });
        })
            ->with('guidanceAssociate')
            ->orderBy('available_date', 'desc')
            ->orderBy('start_time')
            ->get();

        $appointments = Appointment::whereHas('guidanceAssociate', function ($q) {
            $q->whereHas('role', function ($role) {
                $role->whereIn('name', ['guidance_associate', 'admin']);
            });
        })
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
                'guidance_associate_id' => $availability->guidance_associate_id,
                'guidance_associate_name' => $availability->guidanceAssociate ? $availability->guidanceAssociate->full_name : '',
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
                ];
            }
        }

        $guidanceAssociates = User::where('status', 'active')->whereHas('role', function ($q) {
            $q->whereIn('name', ['guidance_associate', 'admin']);
        })->orderBy('first_name')->get();

        return view('admin.availability', compact('availabilities', 'availabilityMap', 'appointmentsMap', 'guidanceAssociates'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'guidance_associate_id' => ['required', 'exists:users,id', function ($attribute, $value, $fail) {
                $user = User::with('role')->find($value);
                if (!$user || $user->status !== 'active' || !$user->role || !in_array($user->role->name, ['guidance_associate', 'admin'], true)) {
                    $fail('The selected provider must be an active counselor or guidance associate.');
                }
            }],
            'available_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
        ]);

        // Check for overlapping availability
        $overlap = Availability::where('guidance_associate_id', $request->guidance_associate_id)
            ->where('available_date', $request->available_date)
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['overlap' => 'This time range overlaps with existing availability for this guidance associate.']);
        }

        DB::transaction(function () use ($request) {
            $availability = Availability::create([
                'guidance_associate_id' => $request->guidance_associate_id,
                'available_date' => $request->available_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'slot_duration' => $request->slot_duration,
                'status' => 'available',
            ]);

            ActivityLog::log('CREATE_AVAILABILITY', "Created availability for {$request->available_date} from {$request->start_time} to {$request->end_time} for guidance associate #{$request->guidance_associate_id}", 'Availability', Auth::id());
        });

        return redirect()->route('admin.availability')->with('success', 'Availability added successfully!');
    }

    public function adminEdit(Availability $availability)
    {
        $guidanceAssociates = User::where('status', 'active')->whereHas('role', function ($q) {
            $q->whereIn('name', ['guidance_associate', 'admin']);
        })->get();

        return view('admin.availability.edit', compact('availability', 'guidanceAssociates'));
    }

    public function adminUpdate(Request $request, Availability $availability)
    {
        $request->validate([
            'guidance_associate_id' => ['required', 'exists:users,id', function ($attribute, $value, $fail) {
                $user = User::with('role')->find($value);
                if (!$user || $user->status !== 'active' || !$user->role || !in_array($user->role->name, ['guidance_associate', 'admin'], true)) {
                    $fail('The selected provider must be an active counselor or guidance associate.');
                }
            }],
            'available_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
        ]);

        // Check for overlapping availability (excluding current)
        $overlap = Availability::where('guidance_associate_id', $request->guidance_associate_id)
            ->where('id', '!=', $availability->id)
            ->where('available_date', $request->available_date)
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['overlap' => 'This time range overlaps with existing availability for this guidance associate.']);
        }

        // If status is booked, don't allow changing date/time
        if ($availability->status === 'booked') {
            return back()->withErrors(['error' => 'Cannot modify a booked slot.']);
        }

        $availability->update([
            'guidance_associate_id' => $request->guidance_associate_id,
            'available_date' => $request->available_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'slot_duration' => $request->slot_duration,
        ]);

        ActivityLog::log('UPDATE_AVAILABILITY', "Updated availability #{$availability->id}", 'Availability', Auth::id());

        return redirect()->route('admin.availability')->with('success', 'Availability updated successfully!');
    }

    public function adminDestroy(Availability $availability)
    {
        if ($availability->status === 'booked') {
            return back()->withErrors(['error' => 'Cannot delete a booked slot.']);
        }

        $availability->delete();

        ActivityLog::log('DELETE_AVAILABILITY', "Deleted availability #{$availability->id}", 'Availability', Auth::id());

        return redirect()->route('admin.availability')->with('success', 'Availability deleted successfully!');
    }
}