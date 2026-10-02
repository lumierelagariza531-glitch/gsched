<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_credentials_open_the_admin_area(): void
    {
        $this->seed(RoleSeeder::class);
        User::create([
            'role_id' => Role::where('name', 'student')->value('id'),
            'first_name' => 'Wrong',
            'last_name' => 'Role',
            'email' => 'admin@g-sched.test',
            'password' => 'old-password',
            'status' => 'inactive',
        ]);
        $this->seed(UserSeeder::class);

        $this->post(route('login'), [
            'email' => 'admin@g-sched.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk();
    }
}
