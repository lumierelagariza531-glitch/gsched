<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentScheduleSchoolFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_schedules_include_only_associates_from_their_school(): void
    {
        $studentRole = Role::create(['name' => 'student']);
        $associateRole = Role::create(['name' => 'guidance_associate']);
        $adminRole = Role::create(['name' => 'admin']);

        $student = $this->makeUser($studentRole, 'Student', 'STCS');
        $stcsAssociate = $this->makeUser($associateRole, 'STCS Guide', 'STCS');
        $otherAssociate = $this->makeUser($associateRole, 'SNHS Guide', 'SNHS');
        $admin = $this->makeUser($adminRole, 'Counselor', null);

        foreach ([$stcsAssociate, $otherAssociate, $admin] as $provider) {
            Availability::create([
                'guidance_associate_id' => $provider->id,
                'available_date' => now()->addDays(5)->toDateString(),
                'start_time' => '09:00',
                'end_time' => '09:30',
                'slot_duration' => 30,
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($student)->get(route('student.schedules'));

        $response->assertOk()
            ->assertSee('id="sidebarToggle"', false)
            ->assertSee('id="themeToggle"', false)
            ->assertSee('.app-navbar #sidebarToggle', false)
            ->assertSee('.app-navbar #themeToggle', false)
            ->assertSee('width: 44px;', false)
            ->assertSee('transform: translateX(-100%) !important;', false)
            ->assertSee('STCS Guide')
            ->assertSee('Counselor')
            ->assertDontSee('SNHS Guide')
            ->assertSee('name="appointment_mode"', false)
            ->assertSee('value="online"', false)
            ->assertSee('value="in_person"', false);
    }

    public function test_student_slot_endpoint_excludes_associates_from_other_schools(): void
    {
        $studentRole = Role::create(['name' => 'student']);
        $associateRole = Role::create(['name' => 'guidance_associate']);
        $student = $this->makeUser($studentRole, 'Student', 'STCS');
        $stcsAssociate = $this->makeUser($associateRole, 'STCS Guide', 'STCS');
        $otherAssociate = $this->makeUser($associateRole, 'SNHS Guide', 'SNHS');
        $date = now()->addDays(5)->toDateString();

        foreach ([$stcsAssociate, $otherAssociate] as $provider) {
            Availability::create([
                'guidance_associate_id' => $provider->id,
                'available_date' => $date,
                'start_time' => '09:00',
                'end_time' => '09:30',
                'slot_duration' => 30,
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($student)
            ->getJson(route('student.schedules.slots', ['date' => $date]));

        $response->assertOk()
            ->assertJsonFragment(['guidance_associate_name' => 'STCS Guide Test'])
            ->assertJsonMissing(['guidance_associate_name' => 'SNHS Guide Test']);
    }

    public function test_coaching_booking_rejects_an_associate_from_another_school(): void
    {
        $studentRole = Role::create(['name' => 'student']);
        $associateRole = Role::create(['name' => 'guidance_associate']);
        $student = $this->makeUser($studentRole, 'Student', 'STCS');
        $otherAssociate = $this->makeUser($associateRole, 'SNHS Guide', 'SNHS');
        $date = now()->addDays(5)->toDateString();
        $availability = Availability::create([
            'guidance_associate_id' => $otherAssociate->id,
            'available_date' => $date,
            'start_time' => '09:00',
            'end_time' => '09:30',
            'slot_duration' => 30,
            'status' => 'available',
        ]);

        $response = $this->actingAs($student)->post(route('student.appointments.store'), [
            'availability_id' => $availability->id,
            'appointment_date' => $date,
            'start_time' => '09:00',
            'end_time' => '09:30',
            'purpose' => 'Academic planning',
            'service_type' => 'Coaching',
            'concern_category' => 'Academic Concerns',
            'appointment_mode' => 'in_person',
        ]);

        $response->assertSessionHasErrors('availability_id');
    }

    private function makeUser(Role $role, string $firstName, ?string $school): User
    {
        return User::create([
            'role_id' => $role->id,
            'first_name' => $firstName,
            'last_name' => 'Test',
            'email' => strtolower(str_replace(' ', '.', $firstName)) . '@example.test',
            'password' => 'test-password',
            'school' => $school,
            'status' => 'active',
        ]);
    }
}
