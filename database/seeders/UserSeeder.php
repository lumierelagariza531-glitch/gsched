<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $guidanceRole = Role::where('name', 'guidance_associate')->first();
        $studentRole = Role::where('name', 'student')->first();

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@g-sched.test'],
            [
                'role_id' => $adminRole->id,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@g-sched.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // Guidance Associate
        User::firstOrCreate(
            ['email' => 'guidance@g-sched.test'],
            [
                'role_id' => $guidanceRole->id,
                'first_name' => 'Guidance',
                'last_name' => 'Associate',
                'email' => 'guidance@g-sched.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // Student
        User::firstOrCreate(
            ['email' => 'student@g-sched.test'],
            [
                'role_id' => $studentRole->id,
                'student_id' => 'STU001',
                'first_name' => 'Student',
                'last_name' => 'User',
                'email' => 'student@g-sched.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // Additional test students
        User::firstOrCreate(
            ['email' => 'john.doe@student.test'],
            [
                'role_id' => $studentRole->id,
                'student_id' => 'STU002',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@student.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'jane.smith@student.test'],
            [
                'role_id' => $studentRole->id,
                'student_id' => 'STU003',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@student.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // Additional guidance associates
        User::firstOrCreate(
            ['email' => 'counselor1@g-sched.test'],
            [
                'role_id' => $guidanceRole->id,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'counselor1@g-sched.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'counselor2@g-sched.test'],
            [
                'role_id' => $guidanceRole->id,
                'first_name' => 'James',
                'last_name' => 'Wilson',
                'email' => 'counselor2@g-sched.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
    }
}