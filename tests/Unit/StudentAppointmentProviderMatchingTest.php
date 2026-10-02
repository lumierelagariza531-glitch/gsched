<?php

namespace Tests\Unit;

use App\Http\Controllers\StudentAppointmentController;
use App\Models\Availability;
use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class StudentAppointmentProviderMatchingTest extends TestCase
{
    public function test_guidance_associate_must_match_the_students_school(): void
    {
        $this->assertTrue($this->availabilityIsEligible('guidance_associate', 'STCS', 'STCS'));
        $this->assertFalse($this->availabilityIsEligible('guidance_associate', 'SNHS', 'STCS'));
        $this->assertFalse($this->availabilityIsEligible('guidance_associate', null, 'STCS'));
    }

    public function test_counseling_admin_matching_does_not_require_a_school_match(): void
    {
        $this->assertTrue($this->availabilityIsEligible('admin', 'SNHS', 'STCS', 'admin'));
    }

    public function test_provider_role_must_match_the_concern_route(): void
    {
        $this->assertFalse($this->availabilityIsEligible('admin', 'STCS', 'STCS', 'guidance_associate'));
    }

    private function availabilityIsEligible(
        string $providerRole,
        ?string $providerSchool,
        ?string $studentSchool,
        string $eligibleRole = 'guidance_associate'
    ): bool {
        $provider = new User(['status' => 'active', 'school' => $providerSchool]);
        $provider->setRelation('role', new Role(['name' => $providerRole]));

        $availability = new Availability(['status' => 'available']);
        $availability->setRelation('guidanceAssociate', $provider);

        $method = new ReflectionMethod(StudentAppointmentController::class, 'hasEligibleProvider');

        return $method->invoke(new StudentAppointmentController(), $availability, $eligibleRole, $studentSchool);
    }
}
