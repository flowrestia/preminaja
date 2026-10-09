<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case REPLIED = 'replied';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Menunggu Respon',
            self::REPLIED => 'Dibalas',
            self::CLOSED => 'Ditutup',
        };
    }
}
