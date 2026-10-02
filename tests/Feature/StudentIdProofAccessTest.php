<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentIdProofAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_only_their_own_saved_proof_images(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent('owner');
        $otherStudent = $this->makeStudent('other');

        $response = $this->actingAs($student)
            ->get(route('student-id-proofs.show', [$student, 'front']));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        foreach (['private', 'no-store', 'no-cache', 'max-age=0'] as $directive) {
            $this->assertStringContainsString($directive, $response->headers->get('Cache-Control'));
        }
        $this->assertSame($this->fakeImageContents(), $response->getContent());

        $this->get(route('student-id-proofs.show', [$otherStudent, 'front']))->assertForbidden();
    }

    public function test_private_proof_endpoint_requires_authentication(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent('guest');

        $this->get(route('student-id-proofs.show', [$student, 'front']))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_student_proofs_and_the_admin_detail_shows_the_secure_images(): void
    {
        Storage::fake('local');
        $admin = $this->makeUser('admin', 'admin');
        $student = $this->makeStudent('admin-view');

        $this->actingAs($admin)
            ->get(route('student-id-proofs.show', [$student, 'back']))
            ->assertOk();

        $detail = $this->get(route('admin.users.edit', $student));
        $detail->assertOk()
            ->assertSee(route('student-id-proofs.show', [$student, 'front']), false)
            ->assertSee('Front of student ID');
    }

    public function test_admin_appointment_details_show_both_student_id_proof_images(): void
    {
        Storage::fake('local');
        $admin = $this->makeUser('appointment-admin', 'admin');
        $student = $this->makeStudent('appointment-proof');
        $guidance = $this->makeUser('appointment-guidance', 'guidance_associate', 'STCS');
        $appointment = $this->makeAppointment($student, $guidance, 'low');

        $detail = $this->actingAs($admin)
            ->get(route('admin.appointments.show', $appointment));

        $detail->assertOk()
            ->assertSee(route('student-id-proofs.show', [$student, 'front']), false)
            ->assertSee(route('student-id-proofs.show', [$student, 'back']), false)
            ->assertSee('Front of student ID')
            ->assertSee('Back of student ID')
            ->assertSee('Counselor')
            ->assertSee($guidance->full_name)
            ->assertSee($guidance->email)
            ->assertSee('STCS Guidance Associate')
            ->assertDontSee('storage/student-id-proofs');

        $this->assertStudentCardDoesNotContainLabel($detail->getContent(), 'STCS Guidance Associate');
        $this->assertAssociateCardContainsLabel($detail->getContent(), 'STCS Guidance Associate');

        foreach (['front', 'back'] as $side) {
            $this->get(route('student-id-proofs.show', [$student, $side]))
                ->assertOk()
                ->assertHeader('Content-Type', 'image/png');
        }
    }

    public function test_assigned_guidance_associate_can_view_proofs_for_non_high_severity_appointment(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent('assigned');
        $guidance = $this->makeUser('assigned-guide', 'guidance_associate', 'STCS');
        $appointment = $this->makeAppointment($student, $guidance, 'low');

        $response = $this->actingAs($guidance)
            ->get(route('guidance.appointments.id-proof', [$appointment, 'front']));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/png');
        foreach (['private', 'no-store', 'no-cache', 'max-age=0'] as $directive) {
            $this->assertStringContainsString($directive, $response->headers->get('Cache-Control'));
        }

        $detail = $this->get(route('guidance.appointments.show', $appointment));
        $detail->assertOk()
            ->assertSee(route('guidance.appointments.id-proof', [$appointment, 'front']), false)
            ->assertSee('Front of student ID')
            ->assertSee('STCS Guidance Associate')
            ->assertSee($guidance->full_name)
            ->assertSee($guidance->email);
        $this->assertStudentCardDoesNotContainLabel($detail->getContent(), 'STCS Guidance Associate');
        $this->assertAssociateCardContainsLabel($detail->getContent(), 'STCS Guidance Associate');

        $requestDetail = $this->get(route('guidance.requests.show', $appointment));
        $requestDetail->assertOk()
            ->assertSee(route('guidance.appointments.id-proof', [$appointment, 'front']), false)
            ->assertSee('Front of student ID')
            ->assertSee('STCS Guidance Associate');
    }

    public function test_school_specific_guidance_associate_labels_render_in_admin_and_guidance_appointment_details(): void
    {
        $admin = $this->makeUser('school-label-admin', 'admin');

        foreach (['STCS', 'SOE'] as $school) {
            $student = $this->makeStudent("school-label-{$school}");
            $guidance = $this->makeUser("school-label-guide-{$school}", 'guidance_associate', $school);
            $appointment = $this->makeAppointment($student, $guidance, 'low');
            $label = "{$school} Guidance Associate";

            $adminDetail = $this->actingAs($admin)
                ->get(route('admin.appointments.show', $appointment));
            $adminDetail->assertOk()
                ->assertSee($label)
                ->assertSee($guidance->full_name)
                ->assertSee($guidance->email);
            $this->assertStudentCardDoesNotContainLabel($adminDetail->getContent(), $label);
            $this->assertAssociateCardContainsLabel($adminDetail->getContent(), $label);

            $guidanceDetail = $this->actingAs($guidance)
                ->get(route('guidance.appointments.show', $appointment));
            $guidanceDetail->assertOk()
                ->assertSee($label);
            $this->assertStudentCardDoesNotContainLabel($guidanceDetail->getContent(), $label);
            $this->assertAssociateCardContainsLabel($guidanceDetail->getContent(), $label);

            $requestDetail = $this->get(route('guidance.requests.show', $appointment));
            $requestDetail->assertOk()->assertSee($label);
        }
    }

    public function test_guidance_cannot_view_unassigned_or_high_severity_student_proofs(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent('restricted');
        $assigned = $this->makeUser('assigned-guide', 'guidance_associate', 'STCS');
        $otherGuidance = $this->makeUser('other-guide', 'guidance_associate');
        $unassignedAppointment = $this->makeAppointment($student, $assigned, 'low');
        $highSeverityAppointment = $this->makeAppointment($student, $assigned, 'high');

        $this->actingAs($otherGuidance)
            ->get(route('guidance.appointments.id-proof', [$unassignedAppointment, 'front']))
            ->assertForbidden();

        $this->actingAs($assigned)
            ->get(route('guidance.appointments.id-proof', [$highSeverityAppointment, 'front']))
            ->assertNotFound();

        $highSeverityRequestDetail = $this->actingAs($assigned)->get(route('guidance.requests.show', $highSeverityAppointment));
        $highSeverityRequestDetail->assertOk()
            ->assertSee('Confidential Student')
            ->assertDontSee(route('guidance.appointments.id-proof', [$highSeverityAppointment, 'front']), false)
            ->assertDontSee($student->full_name)
            ->assertDontSee($student->email);

        $highSeverityDetail = $this->get(route('guidance.appointments.show', $highSeverityAppointment));
        $highSeverityDetail->assertOk()
            ->assertSee('Confidential Student')
            ->assertDontSee(route('guidance.appointments.id-proof', [$highSeverityAppointment, 'front']), false)
            ->assertDontSee($student->full_name)
            ->assertDontSee($student->email);
    }

    public function test_invalid_side_missing_image_and_corrupt_database_path_return_404(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent('missing');
        $this->actingAs($student)
            ->get(route('student-id-proofs.show', [$student, 'left']))
            ->assertNotFound();

        $student->student_id_back = null;
        $student->save();
        $this->get(route('student-id-proofs.show', [$student, 'back']))->assertNotFound();

        $student->student_id_front = 'student-id-proofs/../outside.png';
        $student->save();
        $this->get(route('student-id-proofs.show', [$student, 'front']))->assertNotFound();
    }

    public function test_student_profile_uses_authenticated_private_image_routes(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent('profile');

        $this->actingAs($student)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee(route('student-id-proofs.show', [$student, 'front']), false)
            ->assertSee(route('student-id-proofs.show', [$student, 'back']), false)
            ->assertDontSee('storage/student-id-proofs');
    }

    private function makeStudent(string $suffix): User
    {
        $student = $this->makeUser("student-{$suffix}", 'student');
        $student->forceFill([
            'student_id' => '23-1-'.str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT),
            'student_id_front' => "student-id-proofs/{$student->id}/front.png",
            'student_id_back' => "student-id-proofs/{$student->id}/back.png",
        ])->save();

        Storage::disk('local')->put($student->student_id_front, $this->fakeImageContents());
        Storage::disk('local')->put($student->student_id_back, $this->fakeImageContents());

        return $student;
    }

    private function makeUser(string $name, string $roleName, ?string $school = null): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::create([
            'role_id' => $role->id,
            'first_name' => $name,
            'last_name' => 'Tester',
            'email' => "{$name}@example.test",
            'password' => 'password123',
            'school' => $school ?? ($roleName === 'student' ? 'STCS' : null),
            'status' => 'active',
        ]);
    }

    private function makeAppointment(User $student, User $guidance, string $severity): Appointment
    {
        $status = AppointmentStatus::firstOrCreate(
            ['name' => 'approved'],
            ['label' => 'Approved', 'color' => '#198754']
        );

        return Appointment::create([
            'student_id' => $student->id,
            'guidance_associate_id' => $guidance->id,
            'appointment_status_id' => $status->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'purpose' => 'Synthetic test appointment',
            'service_type' => 'Counseling',
            'concern_category' => 'Personal Concerns',
            'appointment_mode' => 'in_person',
            'severity' => $severity,
        ]);
    }

    private function fakeImageContents(): string
    {
        return file_get_contents(public_path('images/login-bg.png'));
    }

    private function assertStudentCardContainsLabel(string $content, string $label): void
    {
        $document = new \DOMDocument();
        $previousErrorMode = libxml_use_internal_errors(true);
        $document->loadHTML($content);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorMode);
        $studentCards = (new \DOMXPath($document))->query(
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' appointment-student-card ')]"
        );

        $this->assertSame(1, $studentCards->length);
        $this->assertStringContainsString($label, $studentCards->item(0)->textContent);
    }

    private function assertStudentCardDoesNotContainLabel(string $content, string $label): void
    {
        $document = new \DOMDocument();
        $previousErrorMode = libxml_use_internal_errors(true);
        $document->loadHTML($content);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorMode);
        $studentCards = (new \DOMXPath($document))->query(
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' appointment-student-card ')]"
        );

        $this->assertSame(1, $studentCards->length);
        $this->assertStringNotContainsString($label, $studentCards->item(0)->textContent);
    }

    private function assertAssociateCardContainsLabel(string $content, string $label): void
    {
        $document = new \DOMDocument();
        $previousErrorMode = libxml_use_internal_errors(true);
        $document->loadHTML($content);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorMode);
        $associateCards = (new \DOMXPath($document))->query(
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' appointment-associate-card ')]"
        );

        $this->assertSame(1, $associateCards->length);
        $this->assertStringContainsString($label, $associateCards->item(0)->textContent);
    }

}
