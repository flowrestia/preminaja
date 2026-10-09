<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case SELLER = 'seller';
    case BUYER = 'buyer';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Administrator',
            self::SELLER => 'Seller',
            self::BUYER => 'Buyer',
        };
    }

    /**
     * Array of all role names for validation and sync.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
