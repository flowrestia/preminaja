<?php

namespace App\Enums;

enum CredentialStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case DELIVERED = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::RESERVED => 'Dipesan',
            self::DELIVERED => 'Terkirim',
        };
    }
}
