<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Company;
use App\Enums\DocumentType;
use App\Enums\CompanyStatus;
use App\Models\ActivityType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;
   
    public function test_uses_correct_enum_casting()
    {
        $company = Company::factory()->create([
            'status' => CompanyStatus::ACTIVE,
            'document_type' => DocumentType::DNI
        ]);
        
        $this->assertInstanceOf(CompanyStatus::class, $company->status);
        $this->assertInstanceOf(DocumentType::class, $company->document_type);
    }


    public function test_can_have_multiple_activity_types()
    {
        $company = Company::factory()->create();
        $activityTypes = ActivityType::factory()->count(3)->create();
        
        $company->activityTypes()->attach($activityTypes->pluck('id'));
        
        $this->assertCount(3, $company->activityTypes);
    }
}

