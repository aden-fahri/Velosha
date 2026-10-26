<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @brief Model penilaian kepuasan layanan helpdesk oleh pelapor (skor 1-5 bintang).
 */
class SatisfactionRating extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'score',
        'feedback',
        'created_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'created_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }
}
