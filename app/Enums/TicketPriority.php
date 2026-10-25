<?php

namespace App\Enums;

enum TicketPriority: string
{
    case LOW = 'Low';
    case MID = 'Mid';
    case HIGH = 'High';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MID => 'Mid',
            self::HIGH => 'High',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
