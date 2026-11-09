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

    public const STATUS_SUBMITTED = EventStatus::SUBMITTED->value;

    public const STATUS_PUBLISHED = EventStatus::PUBLISHED->value;

    public const STATUS_REJECTED = EventStatus::REJECTED->value;

    public const STATUS_COMPLETED = EventStatus::COMPLETED->value;

    public const STATUS_CANCELLED = EventStatus::CANCELLED->value;

    protected $fillable = [
        'user_id',
        'organization_id',
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
        'rejection_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event): void {
            $event->slug = static::generateUniqueSlug($event->slug ?: $event->title);
        });

        static::saving(function (Event $event): void {
            if (! $event->organization_id && \Illuminate\Support\Facades\Auth::check() && ! \Illuminate\Support\Facades\Auth::user()->isAdmin()) {
                throw new \DomainException('Penyelenggara wajib memilih komunitas / organisasi untuk event ini.');
            }

            if ($event->organization_id) {
                $org = $event->organization ?? Organization::find($event->organization_id);
                if ($org) {
                    // Penyelenggara wajib terverifikasi sebelum dapat mengajukan atau menayangkan event
                    if (in_array($event->status, [self::STATUS_SUBMITTED, self::STATUS_PUBLISHED], true)) {
                        if (! $org->isVerified()) {
                            throw new \DomainException('Komunitas / organisasi belum diverifikasi oleh admin Velosha. Lengkapi verifikasi terlebih dahulu.');
                        }
                    }

                    if ($event->status === self::STATUS_PUBLISHED) {
                        $wasAlreadyPublishedForOrg = $event->exists
                            && $event->getOriginal('status') === self::STATUS_PUBLISHED
                            && (int) $event->getOriginal('organization_id') === (int) $event->organization_id;

                        if (! $wasAlreadyPublishedForOrg) {
                            $currentlyDeducted = $event->exists && (int) $org->quotaLedger()
                                ->where('event_id', $event->id)
                                ->sum('delta') < 0;

                            if (! $currentlyDeducted && ! $org->hasQuota()) {
                                throw new \DomainException('Kuota event organisasi Anda tidak mencukupi (0). Silakan beli paket kuota terlebih dahulu.');
                            }
                        }
                    }
                }
            }
        });

        static::saved(function (Event $event): void {
            if ($event->organization_id) {
                $org = $event->organization ?? Organization::find($event->organization_id);
                if (! $org) {
                    return;
                }

                if ($event->status === self::STATUS_PUBLISHED) {
                    $org->deductQuotaForEvent($event);
                } elseif (in_array($event->status, [self::STATUS_CANCELLED, self::STATUS_REJECTED], true)) {
                    $wasPublished = $event->getOriginal('status') === self::STATUS_PUBLISHED;
                    if ($wasPublished) {
                        $org->refundQuotaForEvent($event);
                    }
                }
            }
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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function submitForReview(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'rejection_note' => null,
        ]);
    }

    public function approve(User $reviewer): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_note' => null,
        ]);
    }

    public function reject(User $reviewer, string $note): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_note' => $note,
        ]);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(EventCategory::class);
    }

    public function registrations(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Registration::class, EventCategory::class);
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
