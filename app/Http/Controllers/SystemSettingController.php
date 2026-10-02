<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->keyBy('setting_key');
        
        $defaults = [
            'system_name' => 'G-SCHED',
            'institution_name' => 'College/University',
            'appointment_duration' => '30',
            'max_appointments_per_day' => '10',
            'working_hours_start' => '08:00',
            'working_hours_end' => '17:00',
            'reminder_24h_enabled' => 'true',
            'reminder_1h_enabled' => 'true',
            'booking_advance_days' => '30',
            'cancellation_cutoff_hours' => '2',
        ];

        foreach ($defaults as $key => $default) {
            if (!isset($settings[$key])) {
                $settings[$key] = (object) ['setting_value' => $default];
            }
        }

        return view('admin.settings', compact('settings'));
    }

    public function schedules()
    {
        return view('admin.schedules');
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:100',
            'institution_name' => 'required|string|max:255',
            'appointment_duration' => 'required|integer|min:15|max:120',
            'max_appointments_per_day' => 'required|integer|min:1|max:50',
            'working_hours_start' => 'required',
            'working_hours_end' => 'required|after:working_hours_start',
            'reminder_24h_enabled' => 'boolean',
            'reminder_1h_enabled' => 'boolean',
            'booking_advance_days' => 'required|integer|min:1|max:365',
            'cancellation_cutoff_hours' => 'required|integer|min:0|max:168',
        ]);

        $settings = [
            'system_name' => $request->system_name,
            'institution_name' => $request->institution_name,
            'appointment_duration' => $request->appointment_duration,
            'max_appointments_per_day' => $request->max_appointments_per_day,
            'working_hours_start' => $request->working_hours_start,
            'working_hours_end' => $request->working_hours_end,
            'reminder_24h_enabled' => $request->boolean('reminder_24h_enabled'),
            'reminder_1h_enabled' => $request->boolean('reminder_1h_enabled'),
            'booking_advance_days' => $request->booking_advance_days,
            'cancellation_cutoff_hours' => $request->cancellation_cutoff_hours,
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::set($key, $value);
        }

        ActivityLog::log('UPDATE_SETTINGS', 'Updated system settings', 'System Settings', Auth::id());

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }
}