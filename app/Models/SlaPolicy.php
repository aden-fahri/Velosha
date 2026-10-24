<?php

namespace App\Models;

use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'priority',
        'response_minutes',
        'resolution_minutes',
    ];

    protected $casts = [
        'priority' => TicketPriority::class,
        'response_minutes' => 'integer',
        'resolution_minutes' => 'integer',
    ];

    public static function forPriority(TicketPriority $priority): ?self
    {
        return static::where('priority', $priority->value)->first();
    }
}
