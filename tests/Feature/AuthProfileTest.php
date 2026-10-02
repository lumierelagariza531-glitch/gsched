<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_profile_displays_registered_student_id_as_read_only(): void
    {
        $student = $this->makeStudent();

        $response = $this->actingAs($student)->get(route('profile'));

        $response->assertOk();
        $response->assertSee('Student ID', false);
        $response->assertSee('id="student_id"', false);
        $response->assertSee('value="23-1-12345"', false);
        $response->assertSee('readonly', false);
        $response->assertDontSee('name="student_id"', false);
        $response->assertSee('No ID images saved');
    }

    public function test_student_profile_update_ignores_forged_student_id_and_saves_other_profile_fields(): void
    {
        $student = $this->makeStudent();

        $response = $this->actingAs($student)->put(route('profile.update'), [
            'first_name' => 'Updated',
            'middle_name' => 'Student',
            'last_name' => 'Applicant',
            'school' => 'SNHS',
            'age' => 21,
            'gender' => 'female',
            'student_id' => '99-9-99999',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'first_name' => 'Updated',
            'school' => 'SNHS',
            'age' => 21,
            'gender' => 'female',
            'student_id' => '23-1-12345',
        ]);
    }

    public function test_guidance_associate_profile_uses_department_specific_role_label(): void
    {
        foreach (['STCS', 'SOE'] as $school) {
            $associate = $this->makeGuidanceAssociate($school);

            $response = $this->actingAs($associate)->get(route('profile'));

            $response->assertOk();
            $response->assertSee("{$school} Guidance Associate");
            $this->assertSame(2, substr_count($response->getContent(), "{$school} Guidance Associate"));
            $this->assertTrue($associate->fresh()->isGuidanceAssociate());
        }
    }

    public function test_guidance_associate_profile_falls_back_to_generic_role_label_without_school(): void
    {
        $associate = $this->makeGuidanceAssociate(null);

        $response = $this->actingAs($associate)->get(route('profile'));

        $response->assertOk();
        $response->assertSee('Guidance Associate');
        $this->assertSame(2, substr_count($response->getContent(), 'Guidance Associate'));
        $response->assertDontSee(' Department Guidance Associate');
        $this->assertTrue($associate->fresh()->isGuidanceAssociate());
    }

    public function test_admin_and_guidance_associate_can_save_facebook_profile_link(): void
    {
        foreach (['admin', 'guidance_associate'] as $roleName) {
            $staff = $this->makeStaff($roleName);
            $facebookUrl = 'https://www.facebook.com/' . $roleName . '.profile';

            $this->actingAs($staff)->put(route('profile.update'), [
                'first_name' => $staff->first_name,
                'middle_name' => $staff->middle_name,
                'last_name' => $staff->last_name,
                'facebook_profile_url' => $facebookUrl,
            ])->assertRedirect()->assertSessionHasNoErrors();

            $this->assertSame($facebookUrl, $staff->fresh()->facebook_profile_url);
            $this->actingAs($staff)->get(route('profile'))
                ->assertOk()
                ->assertSee('name="facebook_profile_url"', false)
                ->assertSee('value="' . $facebookUrl . '"', false);
        }
    }

    public function test_staff_profile_rejects_non_facebook_or_non_https_links(): void
    {
        $admin = $this->makeStaff('admin');

        foreach (['http://www.facebook.com/profile', 'https://example.test/profile'] as $url) {
            $this->actingAs($admin)->from(route('profile'))->put(route('profile.update'), [
                'first_name' => $admin->first_name,
                'middle_name' => $admin->middle_name,
                'last_name' => $admin->last_name,
                'facebook_profile_url' => $url,
            ])->assertRedirect(route('profile'))->assertSessionHasErrors('facebook_profile_url');
        }
    }

    public function test_student_cannot_set_a_facebook_profile_link(): void
    {
        $student = $this->makeStudent();

        $this->actingAs($student)->put(route('profile.update'), [
            'first_name' => $student->first_name,
            'middle_name' => $student->middle_name,
            'last_name' => $student->last_name,
            'school' => $student->school,
            'age' => $student->age,
            'gender' => $student->gender,
            'facebook_profile_url' => 'https://www.facebook.com/forged.profile',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertNull($student->fresh()->facebook_profile_url);
    }

    private function makeStudent(): User
    {
        $role = Role::create(['name' => 'student']);

        return User::create([
            'role_id' => $role->id,
            'student_id' => '23-1-12345',
            'first_name' => 'Test',
            'middle_name' => 'Student',
            'last_name' => 'Applicant',
            'email' => 'student@example.test',
            'password' => 'password123',
            'school' => 'STCS',
            'age' => 20,
            'gender' => 'prefer_not_to_say',
            'status' => 'active',
        ]);
    }

    private function makeGuidanceAssociate(?string $school): User
    {
        $role = Role::firstOrCreate(['name' => 'guidance_associate']);

        return User::create([
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'Associate',
            'email' => 'associate-' . ($school ?? 'unassigned') . '@example.test',
            'password' => 'password123',
            'school' => $school,
            'status' => 'active',
        ]);
    }

    private function makeStaff(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::create([
            'role_id' => $role->id,
            'first_name' => 'Staff',
            'last_name' => ucfirst($roleName),
            'email' => $roleName . '@example.test',
            'password' => 'password123',
            'status' => 'active',
        ]);
    }
}
