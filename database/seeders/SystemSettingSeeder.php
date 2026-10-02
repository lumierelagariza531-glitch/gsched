<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['setting_key' => 'system_name', 'setting_value' => 'G-SCHED', 'description' => 'System display name'],
            ['setting_key' => 'institution_name', 'setting_value' => 'College/University', 'description' => 'Institution name'],
            ['setting_key' => 'appointment_duration', 'setting_value' => '30', 'description' => 'Default appointment duration in minutes'],
            ['setting_key' => 'max_appointments_per_day', 'setting_value' => '10', 'description' => 'Maximum appointments per day per counselor'],
            ['setting_key' => 'working_hours_start', 'setting_value' => '08:00', 'description' => 'Working hours start time'],
            ['setting_key' => 'working_hours_end', 'setting_value' => '17:00', 'description' => 'Working hours end time'],
            ['setting_key' => 'reminder_24h_enabled', 'setting_value' => 'true', 'description' => 'Enable 24-hour appointment reminder'],
            ['setting_key' => 'reminder_1h_enabled', 'setting_value' => 'true', 'description' => 'Enable 1-hour appointment reminder'],
            ['setting_key' => 'booking_advance_days', 'setting_value' => '30', 'description' => 'Days in advance students can book'],
            ['setting_key' => 'cancellation_cutoff_hours', 'setting_value' => '2', 'description' => 'Hours before appointment to allow cancellation'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['setting_key' => $setting['setting_key']], $setting);
        }
    }
}