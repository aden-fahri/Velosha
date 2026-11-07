<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_path',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(QuotaPurchase::class);
    }

    public function quotaLedger(): HasMany
    {
        return $this->hasMany(QuotaLedger::class);
    }

    /**
     * Hitung saldo kuota aktif dari akumulasi delta di ledger.
     */
    public function quotaBalance(): int
    {
        return (int) $this->quotaLedger()->sum('delta');
    }

    /**
     * Apakah organisasi memiliki kuota event yang bisa dipakai?
     */
    public function hasQuota(): bool
    {
        return $this->quotaBalance() > 0;
    }

    /**
     * Potong 1 kuota saat event ditayangkan (published).
     * Mencegah pemotongan ganda bila event sedang berstatus terpotong (net delta < 0).
     */
    public function deductQuotaForEvent(Event $event): bool
    {
        if (! $event->id) {
            return false;
        }

        $currentlyDeducted = (int) $this->quotaLedger()
            ->where('event_id', $event->id)
            ->sum('delta') < 0;

        if ($currentlyDeducted) {
            return true;
        }

        if (! $this->hasQuota()) {
            return false;
        }

        QuotaLedger::create([
            'organization_id' => $this->id,
            'delta' => -1,
            'reason' => 'publish_event',
            'event_id' => $event->id,
            'created_at' => now(),
        ]);

        return true;
    }

    /**
     * Kembalikan kuota jika event dibatalkan sebelum ada tiket yang terjual (sesuai PRD §5.5).
     */
    public function refundQuotaForEvent(Event $event): bool
    {
        if (! $event->id) {
            return false;
        }

        // Jika sudah ada tiket yang dibayar, kuota hangus (kewajiban refund ke peserta ada di EO)
        $hasPaidTickets = $event->registrations()
            ->whereIn('status', [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN])
            ->exists();

        if ($hasPaidTickets) {
            return false;
        }

        $currentlyDeducted = (int) $this->quotaLedger()
            ->where('event_id', $event->id)
            ->sum('delta') < 0;

        if ($currentlyDeducted) {
            QuotaLedger::create([
                'organization_id' => $this->id,
                'delta' => 1,
                'reason' => 'refund_event',
                'event_id' => $event->id,
                'created_at' => now(),
            ]);

            return true;
        }

        return false;
    }
}

