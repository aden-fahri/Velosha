<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotaLedger extends Model
{
    use HasFactory;

    protected $table = 'quota_ledger';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'delta',
        'reason',
        'quota_purchase_id',
        'event_id',
        'created_at',
    ];

    protected $casts = [
        'delta' => 'integer',
        'created_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(QuotaPurchase::class, 'quota_purchase_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function reasonLabel(): string
    {
        return match ($this->reason) {
            'purchase' => 'Pembelian Paket Kuota',
            'publish_event', 'event_creation', 'create_event' => 'Pembuatan / Publikasi Event',
            'event_refund', 'refund' => 'Pengembalian Kuota Event',
            'adjustment' => 'Penyesuaian Manual Admin',
            default => ucfirst(str_replace('_', ' ', (string) $this->reason)),
        };
    }

    public function reference(): string
    {
        if ($this->purchase) {
            return $this->purchase->package?->name ?? ('Order #' . $this->purchase->order_id);
        }

        if ($this->event) {
            return $this->event->title;
        }

        return '—';
    }
}

