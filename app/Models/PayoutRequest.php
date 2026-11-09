<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PayoutRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'organization_id',
        'payout_account_id',
        'amount',
        'status',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'reference_number',
        'rejection_note',
    ];

    protected $casts = [
        'amount' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(PayoutAccount::class, 'payout_account_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Setujui pencairan dana dan debit saldo organisasi dari buku kas (atomik).
     */
    public function approve(User $reviewer, ?string $referenceNumber = null): void
    {
        DB::transaction(function () use ($reviewer, $referenceNumber): void {
            /** @var static|null $fresh */
            $fresh = self::query()->whereKey($this->id)->lockForUpdate()->first();

            if (! $fresh || ! $fresh->isPending()) {
                throw new \DomainException('Pengajuan pencairan ini sudah diproses sebelumnya.');
            }

            $currentBalance = $fresh->organization->financialBalance();
            if ($currentBalance < $fresh->amount) {
                throw new \DomainException("Saldo kas organisasi (Rp {$currentBalance}) tidak mencukupi untuk penarikan Rp {$fresh->amount}.");
            }

            $fresh->update([
                'status' => self::STATUS_APPROVED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'reference_number' => $referenceNumber,
                'rejection_note' => null,
            ]);

            FinancialLedger::create([
                'organization_id' => $fresh->organization_id,
                'type' => FinancialLedger::TYPE_PAYOUT,
                'direction' => FinancialLedger::DIRECTION_DEBIT,
                'amount' => $fresh->amount,
                'note' => 'Pencairan dana ke ' . $fresh->account->bank_name . ' (' . $fresh->account->masked_account_number . ')' . ($referenceNumber ? ' Ref: ' . $referenceNumber : ''),
                'created_at' => now(),
            ]);

            $this->status = self::STATUS_APPROVED;
        });
    }

    /**
     * Tolak pengajuan pencairan dana dengan alasan yang jelas.
     */
    public function reject(User $reviewer, string $note): void
    {
        DB::transaction(function () use ($reviewer, $note): void {
            /** @var static|null $fresh */
            $fresh = self::query()->whereKey($this->id)->lockForUpdate()->first();

            if (! $fresh || ! $fresh->isPending()) {
                throw new \DomainException('Pengajuan pencairan ini sudah diproses sebelumnya.');
            }

            $fresh->update([
                'status' => self::STATUS_REJECTED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_note' => $note,
            ]);

            $this->status = self::STATUS_REJECTED;
        });
    }
}
