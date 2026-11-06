<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_WAITING = 'waiting';
    public const STATUS_HELD = 'held';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_RELEASED = 'released';

    protected $fillable = [
        'user_id',
        'event_category_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    /**
     * Scope untuk reservasi yang masih aktif menahan kuota (belum expired & belum release).
     */
    public function scopeActiveHeld(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_HELD)
            ->where('expires_at', '>', now());
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_HELD && $this->expires_at?->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_HELD && $this->expires_at?->isPast();
    }

    public function release(): bool
    {
        return $this->update(['status' => self::STATUS_RELEASED]);
    }

    public function convert(): bool
    {
        return $this->update(['status' => self::STATUS_CONVERTED]);
    }
}
