<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Models\BibCounter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    public const STATUS_PENDING = RegistrationStatus::PENDING->value;

    public const STATUS_PAID = RegistrationStatus::PAID->value;

    public const STATUS_CANCELLED = RegistrationStatus::CANCELLED->value;

    public const STATUS_CHECKED_IN = RegistrationStatus::CHECKED_IN->value;

    protected $fillable = [
        'user_id',
        'event_category_id',
        'bib_number',
        'participant_name',
        'identity_number',
        'birth_date',
        'gender',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'medical_notes',
        'club',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'identity_number' => 'encrypted',
        'emergency_contact_phone' => 'encrypted',
        'medical_notes' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }

    public function isPaid(): bool
    {
        $val = $this->status instanceof RegistrationStatus ? $this->status->value : $this->status;

        return $val === self::STATUS_PAID || $val === self::STATUS_CHECKED_IN;
    }

    public function isCheckedIn(): bool
    {
        $val = $this->status instanceof RegistrationStatus ? $this->status->value : $this->status;

        return $val === self::STATUS_CHECKED_IN;
    }

    public function isPending(): bool
    {
        $val = $this->status instanceof RegistrationStatus ? $this->status->value : $this->status;

        return $val === self::STATUS_PENDING;
    }

    public function isCancelled(): bool
    {
        $val = $this->status instanceof RegistrationStatus ? $this->status->value : $this->status;

        return $val === self::STATUS_CANCELLED;
    }

    /**
     * Umur peserta pada hari race (syarat minimum 17 tahun — PRD Fitur 4).
     */
    public function ageAtRace(): int
    {
        return $this->birth_date->diffInYears($this->category->event->event_date);
    }

    /**
     * Nomor BIB per kode kategori: VLS-{KODE}-{URUTAN 4 DIGIT}.
     *
     * Urutan dihitung lintas seluruh event (global per kode) karena kolom
     * `bib_number` unique di database dan gerbang scanner mencari tiket
     * lewat `/tickets/{bib}` — satu penomoran tunggal per kode supaya nomor
     * tidak pernah tabrakan antar event.
     *
     * Panggil hanya di dalam transaksi setelah baris kategori di-lock,
     * supaya penomoran serial dan tidak tabrakan antar pendaftar.
     */
    public static function generateBibNumber(EventCategory $category): string
    {
        $code = $category->code;

        // Pastikan baris counter ada (idempoten, aman dari race create).
        BibCounter::insertOrIgnore(['code' => $code, 'counter' => 0, 'created_at' => now(), 'updated_at' => now()]);

        $counter = BibCounter::query()->where('code', $code)->lockForUpdate()->first();
        $counter->increment('counter');
        $counter->refresh();

        return sprintf('VLS-%s-%04d', $code, $counter->counter);
    }
}
