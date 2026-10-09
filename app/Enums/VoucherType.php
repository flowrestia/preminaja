<?php

namespace App\Enums;

enum VoucherType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Persentase (%)',
            self::FIXED => 'Nominal Tetap (Rp)',
        };
    }
}
