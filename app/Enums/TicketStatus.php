<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'Open';
    case IN_PROGRESS = 'In-Progress';
    case CLOSED = 'Closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In-Progress',
            self::CLOSED => 'Closed',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
