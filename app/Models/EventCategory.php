<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'is_primary',
        'code',
        'distance_km',
        'elevation_gain_m',
        'price',
        'quota',
        'registered_count',
        'wave_start_time',
        'cut_off_hours',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'distance_km' => 'decimal:2',
        'elevation_gain_m' => 'decimal:2',
        'price' => 'decimal:2',
        'quota' => 'integer',
        'registered_count' => 'integer',
        'cut_off_hours' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $category) {
            if ($category->is_primary && $category->event_id) {
                static::where('event_id', $category->event_id)
                    ->where('id', '!=', $category->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });

        static::deleted(function (self $category) {
            if ($category->is_primary && $category->event_id) {
                $next = static::where('event_id', $category->event_id)->first();
                $next?->update(['is_primary' => true]);
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Pendaftar pada kategori ini.
     *
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function getRemainingAttribute(): int
    {
        return max(0, $this->quota - $this->registered_count);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Sisa slot bebas yang belum dibeli dan belum di-hold oleh peserta lain.
     */
    public function availableSlots(?int $excludeUserId = null): int
    {
        $query = $this->reservations()->activeHeld();
        if ($excludeUserId !== null) {
            $query->where('user_id', '!=', $excludeUserId);
        }
        $held = $query->count();

        return max(0, $this->remaining - $held);
    }
}
