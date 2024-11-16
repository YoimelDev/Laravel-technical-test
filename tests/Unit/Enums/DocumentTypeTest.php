<?php

namespace Tests\Unit\Enums;

use App\Enums\DocumentType;
use Tests\TestCase;

class DocumentTypeTest extends TestCase
{
    public function test_enum_has_expected_cases()
    {
        $this->assertEquals('dni', DocumentType::DNI->value);
        $this->assertEquals('cif', DocumentType::CIF->value);
        $this->assertEquals('nie', DocumentType::NIE->value);
        $this->assertEquals('nif', DocumentType::NIF->value);
        $this->assertEquals('passport', DocumentType::PASSPORT->value);
        $this->assertEquals('other', DocumentType::OTHER->value);
    }

    public function test_values_method_returns_all_cases()
    {
        $expected = ['dni', 'cif', 'nie', 'nif', 'passport', 'other'];
        $this->assertEquals($expected, DocumentType::values());
    }

    public function test_label_method_returns_correct_labels()
    {
        $this->assertEquals('DNI', DocumentType::DNI->label());
        $this->assertEquals('CIF', DocumentType::CIF->label());
        $this->assertEquals('NIE', DocumentType::NIE->label());
        $this->assertEquals('NIF', DocumentType::NIF->label());
        $this->assertEquals('Passport', DocumentType::PASSPORT->label());
        $this->assertEquals('Other', DocumentType::OTHER->label());
    }
}