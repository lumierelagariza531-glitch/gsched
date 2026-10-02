<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementStudentIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_students_with_four_digit_enrollment_ids(): void
    {
        $admin = $this->makeUser('admin', 'Admin');
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $student = $this->makeUser('student', 'Student');

        $this->actingAs($admin)->put(route('admin.users.update', $student), [
            'role_id' => $studentRole->id,
            'first_name' => $student->first_name,
            'middle_name' => $student->middle_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'student_id' => '25-1-0000',
            'school' => 'STCS',
            'age' => 20,
            'gender' => 'prefer_not_to_say',
            'status' => 'active',
        ])->assertRedirect(route('admin.users.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'student_id' => '25-1-0000',
        ]);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'role_id' => $studentRole->id,
            'first_name' => 'New',
            'middle_name' => '',
            'last_name' => 'Student',
            'email' => 'new-student@example.test',
            'student_id' => '25-1-0001',
            'school' => 'STCS',
            'age' => 20,
            'gender' => 'prefer_not_to_say',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
        ])->assertRedirect(route('admin.users.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'new-student@example.test',
            'student_id' => '25-1-0001',
        ]);
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
            'age' => $roleName === 'student' ? 20 : null,
            'gender' => $roleName === 'student' ? 'prefer_not_to_say' : null,
            'status' => 'active',
        ]);
    }
}
