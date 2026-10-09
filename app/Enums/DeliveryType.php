<?php

namespace App\Enums;

enum DeliveryType: string
{
    case AUTOMATED = 'automated';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::AUTOMATED => 'Otomatis (Instant Delivery)',
            self::MANUAL => 'Manual (Seller Delivery)',
        };
    }
}
