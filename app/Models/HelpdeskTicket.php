<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model HelpdeskTicket (Aduan Bantuan Velosha).
 *
 * Catatan Arsitektur:
 * Model dan tabel dinamai HelpdeskTicket / helpdesk_tickets untuk menjaga pemisahan
 * domain yang bersih dengan tabel tiket lomba (race e-ticket: App\Models\Ticket / tickets)
 * tanpa resiko konflik nama atau regresi pada sistem e-ticket QR.
 */
class HelpdeskTicket extends Model
{
    use HasFactory;

    /**
     * Nama tabel sentral helpdesk ticket.
     */
    public const TABLE_NAME = 'helpdesk_tickets';

    protected $table = self::TABLE_NAME;

    protected $fillable = [
        'reporter_id',
        'operator_id',
        'subject',
        'description',
        'priority',
        'status',
        'duplicate_of_id',
        'is_reference',
        'solution_summary',
        'first_response_at',
        'closed_at',
        'resolution_time',
        'response_due_at',
        'resolution_due_at',
    ];

    protected $casts = [
        'priority' => TicketPriority::class,
        'status' => TicketStatus::class,
        'is_reference' => 'boolean',
        'first_response_at' => 'datetime',
        'closed_at' => 'datetime',
        'resolution_time' => 'integer',
        'response_due_at' => 'datetime',
        'resolution_due_at' => 'datetime',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicate_of_id');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(self::class, 'duplicate_of_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class, 'ticket_id');
    }

    public function rating(): HasOne
    {
        return $this->hasOne(SatisfactionRating::class, 'ticket_id');
    }

    public function suggestionFeedbacks(): HasMany
    {
        return $this->hasMany(TicketSuggestionFeedback::class, 'ticket_id');
    }

    public function isOpen(): bool
    {
        return $this->status === TicketStatus::OPEN;
    }

    public function isInProgress(): bool
    {
        return $this->status === TicketStatus::IN_PROGRESS;
    }

    public function isClosed(): bool
    {
        return $this->status === TicketStatus::CLOSED;
    }
}
