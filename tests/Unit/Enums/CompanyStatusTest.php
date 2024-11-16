<?php

namespace Tests\Unit\Enums;

use App\Enums\CompanyStatus;
use Tests\TestCase;

class CompanyStatusTest extends TestCase
{
    public function test_enum_has_expected_cases()
    {
        $this->assertEquals('active', CompanyStatus::ACTIVE->value);
        $this->assertEquals('inactive', CompanyStatus::INACTIVE->value);
    }

    public function test_values_method_returns_all_cases()
    {
        $expected = ['active', 'inactive'];
        $this->assertEquals($expected, CompanyStatus::values());
    }

    public function test_label_method_returns_correct_labels()
    {
        $this->assertEquals('Active', CompanyStatus::ACTIVE->label());
        $this->assertEquals('Inactive', CompanyStatus::INACTIVE->label());
    }
}