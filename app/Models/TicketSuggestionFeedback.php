<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSuggestionFeedback extends Model
{
    use HasFactory;

    protected $table = 'ticket_suggestion_feedback';

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'reporter_id',
        'helpful',
        'created_at',
    ];

    protected $casts = [
        'helpful' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
