<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SETTLEMENT = 'settlement';
    public const STATUS_CAPTURE = 'capture';
    public const STATUS_EXPIRE = 'expire';
    public const STATUS_CANCEL = 'cancel';

    protected $fillable = [
        'registration_id',
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

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function isSettled(): bool
    {
        return in_array($this->status, [self::STATUS_SETTLEMENT, self::STATUS_CAPTURE], true);
    }
}

