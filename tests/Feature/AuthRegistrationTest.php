<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_a_passed_browser_screening(): void
    {
        Role::create(['name' => 'student']);

        $response = $this->post(route('register'), $this->registrationData([
            'screening_passed' => '0',
        ]));

        $response->assertSessionHasErrors('screening_passed');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_persists_date_of_birth_and_saves_id_images_on_private_storage(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Role::create(['name' => 'student']);

        $response = $this->post(route('register'), $this->registrationData([
            'screening_passed' => '1',
        ]));

        $response->assertRedirect(route('student.dashboard'));
        $user = User::where('email', 'new.student@example.test')->firstOrFail();

        $this->assertSame('2005-04-05', $user->date_of_birth->toDateString());
        $this->assertSame(21, $user->age);
        $this->assertStringStartsWith("student-id-proofs/{$user->id}/", $user->student_id_front);
        $this->assertStringStartsWith("student-id-proofs/{$user->id}/", $user->student_id_back);
        Storage::disk('local')->assertExists($user->student_id_front);
        Storage::disk('local')->assertExists($user->student_id_back);
        Storage::disk('public')->assertMissing($user->student_id_front);
        Storage::disk('public')->assertMissing($user->student_id_back);
        $this->get('/storage/'.$user->student_id_front)->assertForbidden();
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_does_not_create_an_account_when_private_image_storage_fails(): void
    {
        Role::create(['name' => 'student']);
        $disk = \Mockery::mock();
        $disk->shouldReceive('putFileAs')->once()->andReturn(false);
        $disk->shouldReceive('deleteDirectory')->once()->andReturn(true);
        Storage::shouldReceive('disk')->with('local')->twice()->andReturn($disk);

        $response = $this->post(route('register'), $this->registrationData([
            'screening_passed' => '1',
        ]));

        $response->assertSessionHasErrors('student_id_front');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_rejects_impossible_future_and_out_of_range_dates_of_birth(): void
    {
        foreach ([
            '2025-02-30',
            now()->addDay()->toDateString(),
            now()->subYears(11)->toDateString(),
            now()->subYears(101)->toDateString(),
        ] as $dateOfBirth) {
            $this->post(route('register'), $this->registrationData([
                'date_of_birth' => $dateOfBirth,
                'screening_passed' => '1',
            ]))->assertSessionHasErrors('date_of_birth');
        }

        $this->assertDatabaseCount('users', 0);
    }

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'New',
            'middle_name' => 'Student',
            'last_name' => 'Applicant',
            'email' => 'new.student@example.test',
            'student_id' => '25-1-12345',
            'school' => 'STCS',
            'date_of_birth' => '2005-04-05',
            'gender' => 'prefer_not_to_say',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'student_id_front' => UploadedFile::fake()->createWithContent('front.png', file_get_contents(public_path('images/login-bg.png'))),
            'student_id_back' => UploadedFile::fake()->createWithContent('back.png', file_get_contents(public_path('images/login-bg.png'))),
            'screening_passed' => '0',
        ], $overrides);
    }
}
