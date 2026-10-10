<?php

namespace App\Enums;

enum BloodType: string
{
    case A = 'A';
    case B = 'B';
    case AB = 'AB';
    case O = 'O';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
