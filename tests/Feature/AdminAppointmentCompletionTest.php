<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAppointmentCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_complete_approved_appointments_and_notifies_student_once(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $admin = $this->makeUser('admin', 'Admin');
        $approved = $this->makeStatus('approved');
        $completed = $this->makeStatus('completed');

        foreach (['in_person', 'online'] as $mode) {
            $appointment = $this->makeAppointment($student, $provider, $approved, $mode);

            $this->actingAs($admin)
                ->post(route('admin.appointments.complete', $appointment))
                ->assertRedirect(route('admin.appointments.show', $appointment))
                ->assertSessionHas('success');

            $this->assertDatabaseHas('appointments', [
                'id' => $appointment->id,
                'appointment_status_id' => $completed->id,
            ]);
            $this->assertNotNull($appointment->fresh()->completed_at);
            $this->assertDatabaseHas('notifications', [
                'user_id' => $student->id,
                'title' => 'Appointment Completed',
                'message' => 'Your guidance appointment has been marked as completed. Please provide feedback.',
                'type' => 'feedback',
                'related_appointment_id' => $appointment->id,
            ]);
            $this->assertDatabaseHas('activity_logs', [
                'user_id' => $admin->id,
                'action' => 'COMPLETE_APPOINTMENT',
                'module' => 'Appointments',
                'description' => "Completed appointment #{$appointment->id} for student {$student->full_name}",
            ]);
        }

        $firstAppointment = Appointment::orderBy('id')->firstOrFail();
        $completedAt = $firstAppointment->completed_at;
        $this->from(route('admin.appointments.show', $firstAppointment))
            ->post(route('admin.appointments.complete', $firstAppointment))
            ->assertRedirect(route('admin.appointments.show', $firstAppointment))
            ->assertSessionHasErrors('error');

        $this->assertSame($completedAt->toDateTimeString(), $firstAppointment->fresh()->completed_at->toDateTimeString());
        $this->assertSame(2, Notification::where('type', 'feedback')->whereIn(
            'related_appointment_id',
            Appointment::pluck('id')
        )->count());
        $this->assertSame(2, ActivityLog::where('action', 'COMPLETE_APPOINTMENT')->count());
    }

    public function test_completion_button_is_only_visible_for_approved_appointments(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $admin = $this->makeUser('admin', 'Admin');

        foreach (['pending', 'approved', 'cancelled', 'completed'] as $name) {
            $statuses[$name] = $this->makeStatus($name);
        }

        foreach ($statuses as $name => $status) {
            $mode = $name === 'approved' ? 'online' : 'in_person';
            $appointment = $this->makeAppointment($student, $provider, $status, $mode);

            $response = $this->actingAs($admin)
                ->get(route('admin.appointments.show', $appointment))
                ->assertOk();

            if ($name === 'approved') {
                $response->assertSee('Mark Counseling Complete')
                    ->assertSee('student will be prompted to provide feedback')
                    ->assertSee('Add or update meeting link');
            } else {
                $response->assertDontSee('Mark Counseling Complete');
            }
        }
    }

    public function test_completed_status_badges_are_green_even_when_status_color_is_pale_blue(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $admin = $this->makeUser('admin', 'Admin');
        $completed = AppointmentStatus::create([
            'name' => 'completed',
            'label' => 'Completed',
            'color' => '#9FE7F5',
        ]);
        $appointment = $this->makeAppointment($student, $provider, $completed, 'in_person');

        $this->actingAs($admin)
            ->get(route('admin.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('background: #198754', false);

        $this->actingAs($provider)
            ->get(route('guidance.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('background: #198754', false);
    }

    public function test_non_admin_cannot_use_admin_completion_route(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $approved = $this->makeStatus('approved');
        $appointment = $this->makeAppointment($student, $provider, $approved, 'in_person');

        $this->actingAs($student)
            ->post(route('admin.appointments.complete', $appointment))
            ->assertForbidden();

        $this->assertSame($approved->id, $appointment->fresh()->appointment_status_id);
        $this->assertSame(0, Notification::count());
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_guidance_completion_keeps_its_existing_redirect_and_shared_side_effects(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $approved = $this->makeStatus('approved');
        $completed = $this->makeStatus('completed');
        $appointment = $this->makeAppointment($student, $provider, $approved, 'in_person');

        $this->actingAs($provider)
            ->post(route('guidance.requests.complete', $appointment))
            ->assertRedirect(route('guidance.appointments'))
            ->assertSessionHas('success', 'Appointment marked as completed!');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_status_id' => $completed->id,
        ]);
        $this->assertNotNull($appointment->fresh()->completed_at);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'type' => 'feedback',
            'related_appointment_id' => $appointment->id,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $provider->id,
            'action' => 'COMPLETE_APPOINTMENT',
            'module' => 'Appointments',
        ]);
    }

    public function test_completion_rejects_non_approved_statuses_without_mutation(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $admin = $this->makeUser('admin', 'Admin');

        foreach (['pending', 'cancelled', 'completed'] as $name) {
            $status = $this->makeStatus($name);
            $appointment = $this->makeAppointment($student, $provider, $status, 'in_person');

            if ($name === 'completed') {
                $appointment->update(['completed_at' => now()->subDay()]);
            }
            $originalCompletedAt = $appointment->fresh()->completed_at?->toDateTimeString();

            $this->actingAs($admin)
                ->from(route('admin.appointments.show', $appointment))
                ->post(route('admin.appointments.complete', $appointment))
                ->assertRedirect(route('admin.appointments.show', $appointment))
                ->assertSessionHasErrors('error');

            $this->assertSame($status->id, $appointment->fresh()->appointment_status_id);
            $this->assertSame($originalCompletedAt, $appointment->fresh()->completed_at?->toDateTimeString());
        }

        $this->assertSame(0, Notification::count());
        $this->assertSame(0, ActivityLog::count());
    }

    private function makeUser(string $roleName, string $firstName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::create([
            'role_id' => $role->id,
            'first_name' => $firstName,
            'last_name' => 'Test',
            'email' => strtolower($firstName) . '@example.test',
            'password' => 'password',
            'school' => $roleName === 'student' ? 'STCS' : null,
            'status' => 'active',
        ]);
    }

    private function makeStatus(string $name): AppointmentStatus
    {
        return AppointmentStatus::create([
            'name' => $name,
            'label' => ucfirst($name),
        ]);
    }

    private function makeAppointment(
        User $student,
        User $provider,
        AppointmentStatus $status,
        string $mode
    ): Appointment {
        return Appointment::create([
            'student_id' => $student->id,
            'guidance_associate_id' => $provider->id,
            'appointment_status_id' => $status->id,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'purpose' => 'Counseling appointment test',
            'service_type' => 'Counseling',
            'concern_category' => 'Personal Concerns',
            'appointment_mode' => $mode,
            'severity' => 'low',
        ]);
    }
}
