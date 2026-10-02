<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'student', 'description' => 'Student user who can book guidance appointments'],
            ['name' => 'guidance_associate', 'description' => 'Guidance associate who manages appointments and availability'],
            ['name' => 'admin', 'description' => 'Administrator with full system access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}