<?php

namespace Tests\Unit\Enums;

use App\Enums\RoleChangeRequestStatus;
use Tests\TestCase;

class RoleChangeRequestStatusTest extends TestCase
{
    public function test_enum_has_expected_cases()
    {
        $this->assertEquals('pending', RoleChangeRequestStatus::PENDING->value);
        $this->assertEquals('approved', RoleChangeRequestStatus::APPROVED->value);
        $this->assertEquals('rejected', RoleChangeRequestStatus::REJECTED->value);
    }

    public function test_values_method_returns_all_cases()
    {
        $expected = ['pending', 'approved', 'rejected'];
        $this->assertEquals($expected, RoleChangeRequestStatus::values());
    }

    public function test_label_method_returns_correct_labels()
    {
        $this->assertEquals('Pending', RoleChangeRequestStatus::PENDING->label());
        $this->assertEquals('Approved', RoleChangeRequestStatus::APPROVED->label());
        $this->assertEquals('Rejected', RoleChangeRequestStatus::REJECTED->label());
    }
}