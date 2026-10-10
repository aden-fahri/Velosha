<?php

namespace App\Enums;

enum EventType: string
{
    case GRAN_FONDO = 'gran_fondo';
    case MEDIO_FONDO = 'medio_fondo';
    case FUN_RIDE = 'fun_ride';
    case CLIMB = 'climb';

    public function label(): string
    {
        return match ($this) {
            self::GRAN_FONDO => 'Gran Fondo',
            self::MEDIO_FONDO => 'Medio Fondo',
            self::FUN_RIDE => 'Fun Ride',
            self::CLIMB => 'KOM Climb',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::CLIMB => 'bg-teal-800',
            self::FUN_RIDE => 'bg-teal-700',
            default => 'bg-race-600',
        };
    }

    public static function options(): array
    {
        return [
            self::GRAN_FONDO->value => self::GRAN_FONDO->label(),
            self::MEDIO_FONDO->value => self::MEDIO_FONDO->label(),
            self::FUN_RIDE->value => self::FUN_RIDE->label(),
            self::CLIMB->value => self::CLIMB->label(),
        ];
    }
}
