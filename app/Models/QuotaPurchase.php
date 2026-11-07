<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class QuotaPurchase extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRE = 'expire';

    protected $fillable = [
        'organization_id',
        'quota_package_id',
        'user_id',
        'order_id',
        'amount',
        'status',
        'snap_token',
        'payment_type',
        'paid_at',
        'payload',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'payload' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(QuotaPackage::class, 'quota_package_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ledgerEntry(): HasOne
    {
        return $this->hasOne(QuotaLedger::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Tandai pembayaran lunas dan tambahkan saldo kuota ke ledger secara atomik.
     */
    public function markAsPaid(?string $paymentType = null, array $payload = []): void
    {
        if ($this->isPaid()) {
            return;
        }

        DB::transaction(function () use ($paymentType, $payload) {
            $this->update([
                'status' => self::STATUS_PAID,
                'payment_type' => $paymentType ?? $this->payment_type,
                'paid_at' => now(),
                'payload' => $payload ?: $this->payload,
            ]);

            QuotaLedger::create([
                'organization_id' => $this->organization_id,
                'delta' => (int) $this->package->events_count,
                'reason' => 'purchase',
                'quota_purchase_id' => $this->id,
                'created_at' => now(),
            ]);
        });
    }
}

