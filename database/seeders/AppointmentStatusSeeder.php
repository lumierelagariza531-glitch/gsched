<?php

namespace Database\Seeders;

use App\Models\AppointmentStatus;
use Illuminate\Database\Seeder;

class AppointmentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'pending', 'label' => 'Pending', 'color' => '#FACC15', 'chart_color' => '#FACC15', 'description' => 'Appointment request submitted, awaiting approval', 'sort_order' => 1],
            ['name' => 'approved', 'label' => 'Approved', 'color' => '#3B82F6', 'chart_color' => '#3B82F6', 'description' => 'Appointment approved by guidance associate', 'sort_order' => 2],
            ['name' => 'rejected', 'label' => 'Rejected', 'color' => '#EF4444', 'chart_color' => '#EF4444', 'description' => 'Appointment request rejected', 'sort_order' => 3],
            ['name' => 'reschedule_requested', 'label' => 'Reschedule Requested', 'color' => '#F97316', 'chart_color' => '#F97316', 'description' => 'Student requested to reschedule', 'sort_order' => 4],
            ['name' => 'rescheduled', 'label' => 'Rescheduled', 'color' => '#8B5CF6', 'chart_color' => '#8B5CF6', 'description' => 'Appointment rescheduled to new time', 'sort_order' => 5],
            ['name' => 'cancelled', 'label' => 'Cancelled', 'color' => '#EF4444', 'chart_color' => '#EF4444', 'description' => 'Appointment cancelled', 'sort_order' => 6],
            ['name' => 'completed', 'label' => 'Completed', 'color' => '#22C55E', 'chart_color' => '#22C55E', 'description' => 'Appointment completed', 'sort_order' => 7],
            ['name' => 'no_show', 'label' => 'No Show', 'color' => '#6B7280', 'chart_color' => '#6B7280', 'description' => 'Student did not attend appointment', 'sort_order' => 8],
        ];

        foreach ($statuses as $status) {
            AppointmentStatus::updateOrCreate(['name' => $status['name']], $status);
        }
    }
}