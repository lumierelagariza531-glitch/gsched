<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationScreeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_a_browser_screening_pass(): void
    {
        $this->makeStudentRole();

        $response = $this->post(route('register'), $this->registrationData());

        $response->assertSessionHasErrors('screening_passed');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_persists_dob_and_saves_id_images_privately(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->makeStudentRole();

        $response = $this->post(route('register'), $this->registrationData([
            'screening_passed' => '1',
            'date_of_birth' => now()->subYears(20)->subDay()->toDateString(),
        ]));

        $response->assertRedirect(route('student.dashboard'));
        $user = User::where('email', 'student@example.test')->firstOrFail();
        $this->assertSame(20, $user->age);
        $this->assertSame(now()->subYears(20)->subDay()->toDateString(), $user->date_of_birth->toDateString());
        Storage::disk('local')->assertExists($user->student_id_front);
        Storage::disk('local')->assertExists($user->student_id_back);
        Storage::disk('public')->assertMissing($user->student_id_front);
        Storage::disk('public')->assertMissing($user->student_id_back);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_accepts_four_digit_enrollment_sequence_student_id(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->makeStudentRole();

        $this->post(route('register'), $this->registrationData([
            'student_id' => '25-1-0000',
            'screening_passed' => '1',
        ]))->assertRedirect(route('student.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'student@example.test',
            'student_id' => '25-1-0000',
        ]);
    }

    private function makeStudentRole(): void
    {
        Role::create(['name' => 'student', 'description' => 'Student']);
    }

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Test',
            'middle_name' => 'Applicant',
            'last_name' => 'Student',
            'email' => 'student@example.test',
            'student_id' => '23-1-12345',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'school' => 'STCS',
            'date_of_birth' => now()->subYears(20)->toDateString(),
            'gender' => 'prefer_not_to_say',
            'student_id_front' => $this->pngUpload('front.png'),
            'student_id_back' => $this->pngUpload('back.png'),
            'screening_passed' => '0',
        ], $overrides);
    }

    private function pngUpload(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, file_get_contents(public_path('images/login-bg.png')));
    }
}
