<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialLedger extends Model
{
    use HasFactory;

    protected $table = 'financial_ledger';

    public $timestamps = false;

    public const TYPE_TICKET_SALE = 'ticket_sale';
    public const TYPE_PLATFORM_FEE = 'platform_fee';
    public const TYPE_REFUND = 'refund';
    public const TYPE_PAYOUT = 'payout';
    public const TYPE_ADJUSTMENT = 'adjustment';

    public const DIRECTION_CREDIT = 'credit';
    public const DIRECTION_DEBIT = 'debit';

    protected $fillable = [
        'organization_id',
        'event_id',
        'registration_id',
        'type',
        'direction',
        'amount',
        'note',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Penjaga integritas akuntansi append-only:
     * Melarang modifikasi dan penghapusan data mutasi kas.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new \DomainException('Buku kas finansial bersifat append-only: mutasi tidak boleh diubah.');
        });

        static::deleting(function (): void {
            throw new \DomainException('Buku kas finansial bersifat append-only: mutasi tidak boleh dihapus.');
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
