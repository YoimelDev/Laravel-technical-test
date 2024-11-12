<?php

namespace App\Enums;

enum RoleType: string
{
    case ADMIN = 'admin';
    case BASIC = 'basic';
    case BUSINESS_OWNER = 'business_owner';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}