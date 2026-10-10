<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceBannerContent extends Model
{
    protected $fillable = [
        'image_path',
    ];

    public static function current(): static
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'image_path' => null,
        ];
    }
}
