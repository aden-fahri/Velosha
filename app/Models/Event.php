<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    public const TYPE_GRAN_FONDO = EventType::GRAN_FONDO->value;

    public const TYPE_MEDIO_FONDO = EventType::MEDIO_FONDO->value;

    public const TYPE_FUN_RIDE = EventType::FUN_RIDE->value;

    public const TYPE_CLIMB = EventType::CLIMB->value;

    public const STATUS_DRAFT = EventStatus::DRAFT->value;

    public const STATUS_PUBLISHED = EventStatus::PUBLISHED->value;

    public const STATUS_COMPLETED = EventStatus::COMPLETED->value;

    public const STATUS_CANCELLED = EventStatus::CANCELLED->value;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'location_name',
        'event_date',
        'start_time',
        'type',
        'status',
        'youtube_video_id',
        'gpx_file_path',
        'map_image_path',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event): void {
            $event->slug = static::generateUniqueSlug($event->slug ?: $event->title);
        });
    }

    public static function generateUniqueSlug(string $source): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $n = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$n;
            $n++;
        }

        return $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(EventCategory::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('event_date', '>=', now()->toDateString());
    }

    public function getLowestPriceAttribute(): ?float
    {
        $min = $this->categories->min('price');

        return $min === null ? null : (float) $min;
    }

    public function getMinRemainingSlotsAttribute(): ?int
    {
        if ($this->categories->isEmpty()) {
            return null;
        }

        return (int) $this->categories->min(fn (EventCategory $category): int => $category->remaining);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_MEDIO_FONDO => 'Medio Fondo',
            self::TYPE_FUN_RIDE => 'Fun Ride',
            self::TYPE_CLIMB => 'KOM Climb',
            default => 'Gran Fondo',
        };
    }
}
