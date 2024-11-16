<?php

namespace Tests\Unit\Enums;

use App\Enums\RoleType;
use Tests\TestCase;

class RoleTypeTest extends TestCase
{
    public function test_enum_has_expected_cases()
    {
        $this->assertEquals('admin', RoleType::ADMIN->value);
        $this->assertEquals('basic', RoleType::BASIC->value);
        $this->assertEquals('business_owner', RoleType::BUSINESS_OWNER->value);
    }

    public function test_values_method_returns_all_cases()
    {
        $expected = ['admin', 'basic', 'business_owner'];
        $this->assertEquals($expected, RoleType::values());
    }
}