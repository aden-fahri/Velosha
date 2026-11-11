<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    use HasFactory;

    public const VERIFICATION_UNVERIFIED = 'unverified';
    public const VERIFICATION_PENDING = 'pending';
    public const VERIFICATION_VERIFIED = 'verified';
    public const VERIFICATION_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_path',
        'verification_status',
        'verification_document_path',
        'verified_at',
        'verified_by',
        'rejection_note',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function verifier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === self::VERIFICATION_VERIFIED;
    }

    public function isPendingVerification(): bool
    {
        return $this->verification_status === self::VERIFICATION_PENDING;
    }

    public function isRejectedVerification(): bool
    {
        return $this->verification_status === self::VERIFICATION_REJECTED;
    }

    public function submitVerification(string $documentPath): void
    {
        $this->update([
            'verification_status' => self::VERIFICATION_PENDING,
            'verification_document_path' => $documentPath,
            'rejection_note' => null,
        ]);
    }

    public function approveVerification(User $admin): void
    {
        $this->update([
            'verification_status' => self::VERIFICATION_VERIFIED,
            'verified_at' => now(),
            'verified_by' => $admin->id,
            'rejection_note' => null,
        ]);
    }

    public function rejectVerification(User $admin, string $note): void
    {
        $this->update([
            'verification_status' => self::VERIFICATION_REJECTED,
            'verified_at' => null,
            'verified_by' => $admin->id,
            'rejection_note' => $note,
        ]);
    }

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

    public function quotaPurchases(): HasMany
    {
        return $this->hasMany(QuotaPurchase::class);
    }

    public function quotaLedger(): HasMany
    {
        return $this->hasMany(QuotaLedger::class);
    }

    public function payoutAccounts(): HasMany
    {
        return $this->hasMany(PayoutAccount::class);
    }

    public function primaryPayoutAccount(): HasOne
    {
        return $this->hasOne(PayoutAccount::class)->where('is_primary', true);
    }

    public function financialLedger(): HasMany
    {
        return $this->hasMany(FinancialLedger::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class);
    }

    /**
     * Hitung saldo kas aktif organisasi dari akumulasi kredit & debet ledger.
     */
    public function financialBalance(): int
    {
        return (int) ($this->financialLedger()
            ->selectRaw("SUM(CASE WHEN direction = 'credit' THEN amount ELSE -amount END) as total")
            ->value('total') ?? 0);
    }

    /**
     * Total dana pada pengajuan penarikan yang masih berstatus pending.
     */
    public function pendingPayoutAmount(): int
    {
        return (int) $this->payoutRequests()
            ->where('status', PayoutRequest::STATUS_PENDING)
            ->sum('amount');
    }

    /**
     * Saldo kas bersih yang siap dicairkan (setelah dikurangi pengajuan pending).
     */
    public function availablePayoutBalance(): int
    {
        return max(0, $this->financialBalance() - $this->pendingPayoutAmount());
    }

    /**
     * Akumulasi total penjualan tiket kotor (gross sales).
     */
    public function totalGrossSales(): int
    {
        return (int) $this->financialLedger()
            ->where('type', FinancialLedger::TYPE_TICKET_SALE)
            ->where('direction', FinancialLedger::DIRECTION_CREDIT)
            ->sum('amount');
    }

    /**
     * Akumulasi total dana yang sudah berhasil ditransfer/dicairkan.
     */
    public function totalPaidOut(): int
    {
        return (int) $this->financialLedger()
            ->where('type', FinancialLedger::TYPE_PAYOUT)
            ->where('direction', FinancialLedger::DIRECTION_DEBIT)
            ->sum('amount');
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

