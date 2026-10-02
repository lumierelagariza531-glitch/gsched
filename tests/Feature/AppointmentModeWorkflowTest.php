<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Availability;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentModeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_booking_requires_an_appointment_mode(): void
    {
        $student = $this->makeUser('student', 'Student');

        $response = $this->actingAs($student)->post(route('student.appointments.store'), [
            'availability_id' => 999,
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'purpose' => 'Academic planning',
            'service_type' => 'Coaching',
            'concern_category' => 'Academic Concerns',
        ]);

        $response->assertSessionHasErrors('appointment_mode');
    }

    public function test_student_can_book_online_or_in_person_and_mode_is_persisted(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $provider->update(['school' => 'STCS']);
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);

        foreach (['online', 'in_person'] as $offset => $mode) {
            $date = now()->addDays($offset + 2)->toDateString();
            $availability = Availability::create([
                'guidance_associate_id' => $provider->id,
                'available_date' => $date,
                'start_time' => '09:00',
                'end_time' => '09:30',
                'slot_duration' => 30,
                'status' => 'available',
            ]);

            $response = $this->actingAs($student)->postJson(route('student.appointments.store'), [
                'availability_id' => $availability->id,
                'appointment_date' => $date,
                'start_time' => '09:00',
                'end_time' => '09:30',
                'purpose' => 'Academic planning',
                'service_type' => 'Coaching',
                'concern_category' => 'Academic Concerns',
                'appointment_mode' => $mode,
                'severity' => 'low',
            ]);

            $response->assertCreated();
            $this->assertDatabaseHas('appointments', [
                'student_id' => $student->id,
                'guidance_associate_id' => $provider->id,
                'appointment_status_id' => $pending->id,
                'appointment_mode' => $mode,
            ]);
        }
    }

    public function test_admin_online_approval_sends_profile_facebook_link_and_provider_can_update_a_meeting_link(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $admin = $this->makeUser('admin', 'Admin');
        $admin->update(['facebook_profile_url' => 'https://www.facebook.com/jocelyn.caing']);
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        $approved = AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $appointment = $this->makeAppointment($student, $provider, $pending, 'online', 'high');

        $this->actingAs($admin)
            ->get(route('admin.appointments.show', $appointment))
            ->assertOk()
            ->assertDontSee('Online meeting link *')
            ->assertDontSee('name="online_meeting_url"', false);

        $this->actingAs($admin)
            ->post(route('admin.appointments.approve', $appointment))
            ->assertRedirect(route('admin.appointments.show', $appointment));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_status_id' => $approved->id,
            'online_meeting_url' => null,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'type' => 'appointment_approved',
            'related_appointment_id' => $appointment->id,
        ]);
        $approvalNotification = Notification::where('user_id', $student->id)
            ->where('related_appointment_id', $appointment->id)
            ->where('type', 'appointment_approved')
            ->firstOrFail();
        $facebookUrl = 'https://www.facebook.com/jocelyn.caing';
        $this->assertSame($facebookUrl, $approvalNotification->facebook_profile_url);
        $this->assertStringContainsString($facebookUrl, $approvalNotification->message);
        $this->actingAs($student)
            ->get(route('notifications.show', $approvalNotification))
            ->assertOk()
            ->assertSee('Contact your counselor on Facebook')
            ->assertSee('href="' . $facebookUrl . '"', false)
            ->assertSee('href="' . route('student.appointments.show', $appointment) . '"', false)
            ->assertSee('View appointment details');

        $this->actingAs($provider)
            ->post(route('guidance.requests.meeting-link', $appointment), [
                'online_meeting_url' => 'https://meet.example.test/session/456',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'online_meeting_url' => 'https://meet.example.test/session/456',
        ]);
        $this->assertSame(1, Notification::where('related_appointment_id', $appointment->id)
            ->where('type', 'appointment_meeting_link')
            ->count());

        $meetingLinkNotification = Notification::where('user_id', $student->id)
            ->where('related_appointment_id', $appointment->id)
            ->where('type', 'appointment_meeting_link')
            ->firstOrFail();
        $updatedMeetingUrl = 'https://meet.example.test/session/456';
        $this->assertStringContainsString($updatedMeetingUrl, $meetingLinkNotification->message);
        $this->actingAs($student)
            ->get(route('notifications.show', $meetingLinkNotification))
            ->assertOk()
            ->assertSee('Join online appointment')
            ->assertSee('href="' . $updatedMeetingUrl . '"', false)
            ->assertSee('target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('href="' . route('student.appointments.show', $appointment) . '"', false);

        $this->actingAs($provider)
            ->get(route('guidance.requests.show', $appointment))
            ->assertOk()
            ->assertSee('Add or update meeting link')
            ->assertSee('https://meet.example.test/session/456');

        $this->actingAs($student)
            ->get(route('student.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('Online')
            ->assertSee('https://meet.example.test/session/456')
            ->assertSee('rel="noopener noreferrer"', false);

        $otherProvider = $this->makeUser('guidance_associate', 'Other Provider');
        $this->actingAs($otherProvider)
            ->post(route('guidance.requests.meeting-link', $appointment), [
                'online_meeting_url' => 'https://meet.example.test/session/789',
            ])
            ->assertForbidden();
    }

    public function test_online_approval_requires_approver_facebook_profile_link(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $admin = $this->makeUser('admin', 'Admin');
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $appointment = $this->makeAppointment($student, $provider, $pending, 'online');

        $this->actingAs($admin)
            ->from(route('admin.appointments.show', $appointment))
            ->post(route('admin.appointments.approve', $appointment), [
                'online_meeting_url' => 'https://meet.example.test/session/123',
            ])
            ->assertRedirect(route('admin.appointments.show', $appointment))
            ->assertSessionHasErrors('facebook_profile_url');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_status_id' => $pending->id,
            'online_meeting_url' => null,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'related_appointment_id' => $appointment->id,
            'type' => 'appointment_approved',
        ]);
    }

    public function test_in_person_approval_notification_does_not_include_a_meeting_link(): void
    {
        $student = $this->makeUser('student', 'Student');
        $admin = $this->makeUser('admin', 'Admin');
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $appointment = $this->makeAppointment(
            $student,
            $this->makeUser('guidance_associate', 'Provider'),
            $pending,
            'in_person'
        );

        $this->actingAs($admin)
            ->post(route('admin.appointments.approve', $appointment))
            ->assertRedirect(route('admin.appointments.show', $appointment));

        $notification = Notification::where('user_id', $student->id)
            ->where('related_appointment_id', $appointment->id)
            ->where('type', 'appointment_approved')
            ->firstOrFail();
        $this->assertStringNotContainsString('http', $notification->message);

        $this->actingAs($student)
            ->get(route('notifications.show', $notification))
            ->assertOk()
            ->assertDontSee('Join online appointment');
    }

    public function test_in_person_appointment_details_show_the_assigned_provider_office(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        $appointment = $this->makeAppointment($student, $provider, $pending, 'in_person');

        $this->actingAs($student)
            ->get(route('student.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('In Person')
            ->assertSee('STCS Faculty Office');
    }

    public function test_guidance_online_approval_does_not_require_a_separate_meeting_url(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $provider->update(['facebook_profile_url' => 'https://www.facebook.com/provider.profile']);
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $appointment = $this->makeAppointment($student, $provider, $pending, 'online');

        $this->actingAs($provider)
            ->post(route('guidance.requests.approve', $appointment))
            ->assertRedirect(route('guidance.requests'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_status_id' => AppointmentStatus::where('name', 'approved')->value('id'),
            'online_meeting_url' => null,
        ]);
    }

    public function test_guidance_associate_can_approve_a_pending_appointment_from_its_details_page(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        $approved = AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $appointment = $this->makeAppointment($student, $provider, $pending, 'in_person');

        $this->actingAs($provider)
            ->get(route('guidance.requests.show', $appointment))
            ->assertOk()
            ->assertSee('href="' . route('guidance.appointments.show', $appointment) . '"', false)
            ->assertSee('Appointment Details');

        $this->get(route('guidance.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('action="' . route('guidance.requests.approve', $appointment) . '"', false)
            ->assertSee('Approve Appointment')
            ->assertDontSee('Mark Completed');

        $this->post(route('guidance.requests.approve', $appointment))
            ->assertRedirect(route('guidance.requests'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_status_id' => $approved->id,
            'online_meeting_url' => null,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'type' => 'appointment_approved',
            'related_appointment_id' => $appointment->id,
        ]);
    }

    public function test_online_approval_is_available_from_appointment_details_without_a_meeting_link(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $provider->update(['facebook_profile_url' => 'https://www.facebook.com/provider.profile']);
        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        $approved = AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $appointment = $this->makeAppointment($student, $provider, $pending, 'online');
        $detailsUrl = route('guidance.appointments.show', $appointment);

        $this->actingAs($provider)
            ->get(route('guidance.appointments.show', $appointment))
            ->assertOk()
            ->assertDontSee('Online meeting link *')
            ->assertDontSee('name="online_meeting_url"', false)
            ->assertSee('Approve Appointment');
        $this->get(route('guidance.requests.show', $appointment))
            ->assertOk()
            ->assertDontSee('Online meeting link *')
            ->assertDontSee('approval_online_meeting_url', false);

        $this->from($detailsUrl)
            ->post(route('guidance.requests.approve', $appointment))
            ->assertRedirect(route('guidance.requests'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_status_id' => $approved->id,
            'online_meeting_url' => null,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'related_appointment_id' => $appointment->id,
            'facebook_profile_url' => 'https://www.facebook.com/provider.profile',
        ]);
    }

    public function test_approved_appointment_details_offer_completion_and_completion_notifies_student(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');
        $approved = AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $completed = AppointmentStatus::create(['name' => 'completed', 'label' => 'Completed']);
        $appointment = $this->makeAppointment($student, $provider, $approved, 'in_person');

        $this->actingAs($provider)
            ->get(route('guidance.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('action="' . route('guidance.requests.complete', $appointment) . '"', false)
            ->assertSee('btn-success', false)
            ->assertSee('Mark Completed')
            ->assertDontSee('Approve Appointment');

        $this->post(route('guidance.requests.complete', $appointment))
            ->assertRedirect(route('guidance.appointments'))
            ->assertSessionHas('success');

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
    }

    public function test_appointment_details_only_show_actions_for_pending_or_approved_statuses(): void
    {
        $student = $this->makeUser('student', 'Student');
        $provider = $this->makeUser('guidance_associate', 'Provider');

        foreach (['pending', 'approved', 'completed', 'cancelled'] as $name) {
            $status = AppointmentStatus::create(['name' => $name, 'label' => ucfirst($name)]);
            $appointment = $this->makeAppointment($student, $provider, $status, 'in_person');

            $response = $this->actingAs($provider)
                ->get(route('guidance.appointments.show', $appointment))
                ->assertOk();

            if ($name === 'pending') {
                $response->assertSee('Approve Appointment')
                    ->assertDontSee('Mark Completed');
            } elseif ($name === 'approved') {
                $response->assertSee('Mark Completed')
                    ->assertDontSee('Approve Appointment');
            } else {
                $response->assertDontSee('Mark Completed')
                    ->assertDontSee('Approve Appointment');
            }
        }
    }

    public function test_guidance_associates_only_see_and_manage_their_assigned_appointments(): void
    {
        $associate = $this->makeUser('guidance_associate', 'Assigned Associate');
        $otherAssociate = $this->makeUser('guidance_associate', 'Other Associate');
        $counselor = $this->makeUser('admin', 'School Counselor');
        $ownStudent = $this->makeUser('student', 'Own Student');
        $otherStudent = $this->makeUser('student', 'Other Student');
        $counselingStudent = $this->makeUser('student', 'Counseling Student');

        $pending = AppointmentStatus::create(['name' => 'pending', 'label' => 'Pending']);
        $approved = AppointmentStatus::create(['name' => 'approved', 'label' => 'Approved']);
        $completed = AppointmentStatus::create(['name' => 'completed', 'label' => 'Completed']);
        AppointmentStatus::create(['name' => 'cancelled', 'label' => 'Cancelled']);

        $ownAppointment = $this->makeAppointment($ownStudent, $associate, $pending, 'in_person');
        $otherDepartmentAppointment = $this->makeAppointment($otherStudent, $otherAssociate, $pending, 'in_person');
        $counselorAppointment = $this->makeAppointment($counselingStudent, $counselor, $pending, 'in_person');
        $otherCompletedAppointment = $this->makeAppointment($otherStudent, $otherAssociate, $completed, 'in_person');
        $otherAvailability = Availability::create([
            'guidance_associate_id' => $otherAssociate->id,
            'available_date' => now()->addDays(5)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '10:30',
            'slot_duration' => 30,
            'status' => 'available',
        ]);

        $this->actingAs($associate)
            ->get(route('guidance.requests'))
            ->assertOk()
            ->assertSee($ownStudent->full_name)
            ->assertDontSee($otherStudent->full_name)
            ->assertDontSee($counselingStudent->full_name);

        $this->get(route('guidance.history'))
            ->assertOk()
            ->assertSee($ownStudent->full_name)
            ->assertDontSee($otherStudent->full_name)
            ->assertDontSee($counselingStudent->full_name);

        $this->get(route('guidance.dashboard'))
            ->assertOk()
            ->assertSee($ownStudent->full_name)
            ->assertDontSee($otherStudent->full_name)
            ->assertDontSee($counselingStudent->full_name);

        $this->get(route('guidance.requests.show', $otherDepartmentAppointment))
            ->assertForbidden();
        $this->get(route('guidance.requests.show', $counselorAppointment))
            ->assertForbidden();
        $this->get(route('guidance.appointments.show', $otherCompletedAppointment))
            ->assertForbidden();

        $this->post(route('guidance.requests.approve', $otherDepartmentAppointment))
            ->assertForbidden();
        $this->post(route('guidance.requests.approve', $counselorAppointment))
            ->assertForbidden();
        $this->post(route('guidance.requests.reject', $otherDepartmentAppointment))
            ->assertForbidden();
        $this->post(route('guidance.requests.reschedule.submit', $ownAppointment), [
            'availability_id' => $otherAvailability->id,
            'requested_date' => $otherAvailability->available_date->toDateString(),
            'requested_start_time' => '10:00',
            'requested_end_time' => '10:30',
            'reason' => 'Move to this department schedule',
        ])->assertNotFound();

        $this->assertDatabaseHas('appointments', [
            'id' => $otherDepartmentAppointment->id,
            'appointment_status_id' => $pending->id,
        ]);
        $this->assertDatabaseHas('appointments', [
            'id' => $counselorAppointment->id,
            'appointment_status_id' => $pending->id,
        ]);
        $this->assertDatabaseHas('appointments', [
            'id' => $ownAppointment->id,
            'appointment_status_id' => $pending->id,
        ]);

        $this->post(route('guidance.requests.approve', $ownAppointment))
            ->assertRedirect(route('guidance.requests'));
        $this->assertDatabaseHas('appointments', [
            'id' => $ownAppointment->id,
            'appointment_status_id' => $approved->id,
        ]);

        $this->actingAs($otherAssociate)
            ->get(route('guidance.appointments.show', $ownAppointment))
            ->assertForbidden();
        $this->post(route('guidance.requests.approve', $ownAppointment))
            ->assertForbidden();
        $this->post(route('guidance.requests.complete', $ownAppointment))
            ->assertForbidden();
    }

    public function test_student_profile_hides_staff_only_sections_and_login_forces_light_theme(): void
    {
        $student = $this->makeUser('student', 'Student');

        $this->actingAs($student)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Assigned School')
            ->assertSee('href="' . route('student.dashboard') . '"', false)
            ->assertSee('id="themeToggle"', false)
            ->assertDontSee('Office')
            ->assertDontSee('License No.')
            ->assertDontSee('Experience &amp; Bio')
            ->assertDontSee('Credentials &amp; Expertise');

        auth()->logout();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('const forceLightTheme = true;')
            ->assertDontSee('id="themeToggle"', false);
    }

    public function test_authenticated_dashboards_use_role_specific_brand_destination_and_theme_toggle(): void
    {
        foreach (['pending', 'approved', 'cancelled', 'completed'] as $name) {
            AppointmentStatus::firstOrCreate(['name' => $name], ['label' => ucfirst($name)]);
        }

        foreach ([
            ['role' => 'student', 'name' => 'Student Dashboard'],
            ['role' => 'guidance_associate', 'name' => 'Guidance Dashboard'],
            ['role' => 'admin', 'name' => 'Admin Dashboard'],
        ] as $index => $case) {
            $user = $this->makeUser($case['role'], 'Dashboard ' . $index);
            $routeName = match ($case['role']) {
                'student' => 'student.dashboard',
                'guidance_associate' => 'guidance.dashboard',
                default => 'admin.dashboard',
            };

            $response = $this->actingAs($user)->get(route($routeName));
            $response->assertOk()
                ->assertSee('href="' . route($routeName) . '"', false)
                ->assertSee('id="themeToggle"', false)
                ->assertSee('href="' . route('profile') . '"', false)
                ->assertDontSee('sidebar-text">Profile', false)
                ->assertDontSee('<span>Profile</span>', false)
                ->assertDontSee('Change Password</a>', false);
        }
    }

    public function test_password_change_form_is_in_profile_settings_and_password_route_remains_secure(): void
    {
        $user = $this->makeUser('student', 'Password');

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('id="password-settings"', false)
            ->assertSee('action="' . route('password.update') . '"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('Current Password')
            ->assertDontSee('href="' . route('password.change') . '"', false);

        $this->actingAs($user)
            ->get(route('password.change'))
            ->assertRedirect(route('profile') . '#password-settings');

        $this->from(route('profile'))
            ->post(route('password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('current_password');

        $this->from(route('profile'))
            ->post(route('password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('profile'));

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password', $user->fresh()->password));
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
            'office_location' => $roleName === 'guidance_associate' ? 'STCS Faculty Office' : null,
            'status' => 'active',
        ]);
    }

    private function makeAppointment(
        User $student,
        User $provider,
        AppointmentStatus $status,
        string $mode,
        string $severity = 'low'
    ): Appointment {
        return Appointment::create([
            'student_id' => $student->id,
            'guidance_associate_id' => $provider->id,
            'appointment_status_id' => $status->id,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'purpose' => 'Guidance appointment test',
            'service_type' => 'Counseling',
            'concern_category' => 'Personal Concerns',
            'appointment_mode' => $mode,
            'severity' => $severity,
        ]);
    }
}
