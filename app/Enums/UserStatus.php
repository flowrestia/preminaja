<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::BLOCKED => 'Diblokir',
        };
    }
}
