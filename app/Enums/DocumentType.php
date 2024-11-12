<?php

namespace App\Enums;

enum DocumentType: string
{
    case DNI = 'dni';
    case CIF = 'cif';
    case NIE = 'nie';
    case NIF = 'nif';
    case PASSPORT = 'passport';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::DNI => 'DNI',
            self::CIF => 'CIF',
            self::NIE => 'NIE',
            self::NIF => 'NIF',
            self::PASSPORT => 'Passport',
            self::OTHER => 'Other',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

