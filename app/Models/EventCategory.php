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
        'distance_km' => 'decimal:2',
        'elevation_gain_m' => 'decimal:2',
        'price' => 'decimal:2',
        'quota' => 'integer',
        'registered_count' => 'integer',
        'cut_off_hours' => 'decimal:2',
    ];

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

    public function getRemainingAttribute(): int
    {
        return max(0, $this->quota - $this->registered_count);
    }
}
