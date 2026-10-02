<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Feedback;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_feedback_answers_automatically_produce_a_half_star_rating(): void
    {
        [$student, $appointment, $guidance] = $this->makeCompletedAppointment();
        $answers = array_fill_keys(array_keys(Feedback::SQD_QUESTIONS), 'agree');
        $answers['sqd0'] = 'strongly_agree';
        $answers['sqd1'] = 'strongly_agree';
        $answers['sqd2'] = 'strongly_agree';
        $answers['sqd3'] = 'strongly_agree';

        $this->actingAs($student)->post(route('student.feedback.store'), $this->feedbackData($appointment, $answers) + ['rating' => '1.0'])
            ->assertRedirect(route('student.feedback.index'));

        $feedback = Feedback::where('appointment_id', $appointment->id)->firstOrFail();
        $this->assertSame('4.5', $feedback->rating);
        $this->assertDatabaseHas('feedback', [
            'appointment_id' => $appointment->id,
            'student_id' => $student->id,
            'rating' => 4.5,
        ]);

        $this->assertStringContainsString('bi-star-half', view('shared.feedback-rating', ['feedback' => $feedback])->render());
        $this->assertStringContainsString('4.5', view('shared.feedback-rating', ['feedback' => $feedback])->render());

        $this->actingAs($guidance)
            ->get(route('guidance.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('4.5')
            ->assertSee('bi-star-half');

        $adminRole = Role::create(['name' => 'admin']);
        $admin = User::create([
            'role_id' => $adminRole->id,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'feedback.admin@example.test',
            'password' => 'password',
            'status' => 'active',
        ]);
        $this->actingAs($admin)
            ->get(route('admin.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('4.5')
            ->assertSee('bi-star-half');
    }

    public function test_student_cannot_submit_invalid_sqd_answers(): void
    {
        [$student, $appointment] = $this->makeCompletedAppointment();

        $answers = array_fill_keys(array_keys(Feedback::SQD_QUESTIONS), 'agree');
        $answers['sqd0'] = 'not_a_real_answer';

        $this->actingAs($student)
            ->post(route('student.feedback.store'), $this->feedbackData($appointment, $answers))
            ->assertSessionHasErrors('sqd0');
        $this->assertDatabaseMissing('feedback', ['appointment_id' => $appointment->id]);
    }

    public function test_only_not_applicable_answers_produce_no_numeric_rating(): void
    {
        [$student, $appointment] = $this->makeCompletedAppointment();

        $answers = array_fill_keys(array_keys(Feedback::SQD_QUESTIONS), 'not_applicable');
        $this->actingAs($student)->post(route('student.feedback.store'), $this->feedbackData($appointment, $answers))
            ->assertRedirect(route('student.feedback.index'));

        $this->assertNull(Feedback::where('appointment_id', $appointment->id)->firstOrFail()->rating);
    }

    public function test_sqd_choices_map_to_the_full_negative_to_positive_range(): void
    {
        $fields = array_keys(Feedback::SQD_QUESTIONS);

        $this->assertSame(1.0, Feedback::calculateRating(array_fill_keys($fields, 'strongly_disagree')));
        $this->assertSame(5.0, Feedback::calculateRating(array_fill_keys($fields, 'strongly_agree')));
        $this->assertSame(3.0, Feedback::calculateRating(array_fill_keys($fields, 'neither')));
    }

    private function makeCompletedAppointment(): array
    {
        $studentRole = Role::create(['name' => 'student']);
        $guidanceRole = Role::create(['name' => 'guidance_associate']);
        $student = User::create([
            'role_id' => $studentRole->id,
            'first_name' => 'Feedback',
            'last_name' => 'Student',
            'email' => 'feedback.student@example.test',
            'password' => 'password',
            'status' => 'active',
        ]);
        $guidance = User::create([
            'role_id' => $guidanceRole->id,
            'first_name' => 'Guidance',
            'last_name' => 'Associate',
            'email' => 'feedback.guidance@example.test',
            'password' => 'password',
            'status' => 'active',
        ]);
        $completed = AppointmentStatus::create(['name' => 'completed', 'label' => 'Completed']);
        $appointment = Appointment::create([
            'student_id' => $student->id,
            'guidance_associate_id' => $guidance->id,
            'appointment_status_id' => $completed->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'purpose' => 'Feedback rating test',
        ]);

        return [$student, $appointment, $guidance];
    }

    private function feedbackData(Appointment $appointment, array $answers): array
    {
        return array_merge([
            'appointment_id' => $appointment->id,
            'suggestions' => 'Helpful session.',
        ], $answers);
    }
}
