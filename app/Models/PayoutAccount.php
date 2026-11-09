<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayoutAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'bank_name',
        'account_number',
        'account_name',
        'is_primary',
    ];

    protected $casts = [
        'account_number' => 'encrypted',
        'is_primary' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class);
    }

    /**
     * Tampilkan nomor rekening tersamarkan (hanya 4 digit terakhir).
     */
    public function getMaskedAccountNumberAttribute(): string
    {
        $raw = (string) $this->account_number;
        $len = strlen($raw);

        if ($len <= 4) {
            return $raw;
        }

        return str_repeat('•', 4) . ' ' . substr($raw, -4);
    }
}
