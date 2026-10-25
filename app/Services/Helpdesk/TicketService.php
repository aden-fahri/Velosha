<?php

namespace App\Services\Helpdesk;

use App\Enums\Helpdesk\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\SlaPolicy;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketService
{
    /**
     * Membuat tiket baru oleh pelapor dengan nomor tiket unik dan perhitungan SLA otomatis.
     * Mengirimkan balasan otomatis (auto-reply) jika tersedia template yang relevan.
     * Kegagalan pada auto-reply dicatat ke log dan tidak membatalkan pembuatan tiket.
     *
     * @param User $reporter
     * @param array{subject: string, description: string, priority: TicketPriority|string, category?: TicketCategory|string} $data
     * @param CarbonInterface|null $now
     * @return HelpdeskTicket
     * @throws DomainException
     */
    public function createTicket(User $reporter, array $data, ?CarbonInterface $now = null): HelpdeskTicket
    {
        $current = $now ? Carbon::instance($now) : Carbon::now();

        $priority = $data['priority'] instanceof TicketPriority
            ? $data['priority']
            : TicketPriority::tryFrom((string) $data['priority']) ?? TicketPriority::LOW;

        $category = isset($data['category'])
            ? ($data['category'] instanceof TicketCategory ? $data['category'] : TicketCategory::tryFrom((string) $data['category']) ?? TicketCategory::LAINNYA)
            : TicketCategory::LAINNYA;

        $slaPolicy = SlaPolicy::where('priority', $priority->value)->first();

        $defaultResponse = match ($priority) {
            TicketPriority::HIGH => 15,
            TicketPriority::MID => 60,
            TicketPriority::LOW => 240,
        };
        $defaultResolution = match ($priority) {
            TicketPriority::HIGH => 240,
            TicketPriority::MID => 1440,
            TicketPriority::LOW => 4320,
        };

        $responseDueMinutes = $slaPolicy?->response_minutes ?? $defaultResponse;
        $resolutionDueMinutes = $slaPolicy?->resolution_minutes ?? $defaultResolution;

        $ticket = DB::transaction(function () use ($reporter, $data, $category, $priority, $current, $responseDueMinutes, $resolutionDueMinutes) {
            return HelpdeskTicket::create([
                'reporter_id' => $reporter->id,
                'operator_id' => null,
                'category' => $category,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'status' => TicketStatus::OPEN,
                'priority' => $priority,
                'response_due_at' => (clone $current)->addMinutes($responseDueMinutes),
                'resolution_due_at' => (clone $current)->addMinutes($resolutionDueMinutes),
                'first_response_at' => null,
                'closed_at' => null,
                'resolution_time' => null,
                'is_reference' => false,
                'solution_summary' => null,
            ]);
        });

        // Hook AutoReplyService::send() di sini. Kegagalan auto-reply dicatat ke log tanpa membatalkan tiket.
        try {
            app(AutoReplyService::class)->send($ticket);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim auto-reply helpdesk: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
                'exception' => $e,
            ]);
        }

        return $ticket;
    }

    /**
     * Klaim tiket oleh operator secara atomik:
     * UPDATE helpdesk_tickets SET operator_id = ? WHERE id = ? AND operator_id IS NULL
     *
     * @param HelpdeskTicket $ticket
     * @param User $operator
     * @return HelpdeskTicket
     * @throws DomainException Jika tiket sudah diklaim operator lain atau status tiket CLOSED.
     * @throws AuthorizationException Jika user bukan OPERATOR/ADMIN.
     */
    public function claimTicket(HelpdeskTicket $ticket, User $operator): HelpdeskTicket
    {
        if ($operator->isSystem()) {
            throw new DomainException('Akun sistem/bot tidak dapat mengklaim tiket.');
        }

        if (! $operator->isOperator() && ! $operator->isAdmin()) {
            throw new AuthorizationException('Hanya operator atau admin yang dapat mengklaim tiket.');
        }

        if ($ticket->isClosed()) {
            throw new DomainException('Tiket yang sudah ditutup tidak dapat diklaim.');
        }

        return DB::transaction(function () use ($ticket, $operator) {
            $affected = DB::table(HelpdeskTicket::TABLE_NAME)
                ->where('id', $ticket->id)
                ->whereNull('operator_id')
                ->where('status', '!=', TicketStatus::CLOSED->value)
                ->update([
                    'operator_id' => $operator->id,
                    'status' => TicketStatus::IN_PROGRESS->value,
                    'updated_at' => Carbon::now(),
                ]);

            if ($affected === 0) {
                $fresh = HelpdeskTicket::find($ticket->id);
                if ($fresh && $fresh->operator_id !== null && $fresh->operator_id !== $operator->id) {
                    throw new DomainException('Tiket sudah diklaim oleh operator lain.');
                }
                if ($fresh && $fresh->isClosed()) {
                    throw new DomainException('Tiket yang sudah ditutup tidak dapat diklaim.');
                }
            }

            return $ticket->fresh();
        });
    }

    /**
     * Menugaskan tiket ke operator tertentu oleh supervisor/admin.
     *
     * @param HelpdeskTicket $ticket
     * @param User $operator
     * @param User $actor
     * @return HelpdeskTicket
     * @throws DomainException Jika tiket sudah CLOSED.
     * @throws AuthorizationException Jika $actor bukan ADMIN/OPERATOR atau target bukan operator/admin.
     */
    public function assignOperator(HelpdeskTicket $ticket, User $operator, User $actor): HelpdeskTicket
    {
        if (! $actor->isOperator() && ! $actor->isAdmin()) {
            throw new AuthorizationException('Hanya operator atau admin yang berwenang menugaskan tiket.');
        }

        if ($operator->isSystem()) {
            throw new DomainException('Tiket tidak dapat ditugaskan kepada akun sistem/bot.');
        }

        if (! $operator->isOperator() && ! $operator->isAdmin()) {
            throw new AuthorizationException('Tiket hanya dapat ditugaskan kepada staf bertugas (operator/admin).');
        }

        if ($ticket->isClosed()) {
            throw new DomainException('Tiket yang sudah ditutup tidak dapat ditugaskan ulang.');
        }

        return DB::transaction(function () use ($ticket, $operator) {
            $lockedTicket = HelpdeskTicket::where('id', $ticket->id)->lockForUpdate()->firstOrFail();

            if ($lockedTicket->isClosed()) {
                throw new DomainException('Tiket yang sudah ditutup tidak dapat ditugaskan ulang.');
            }

            $lockedTicket->operator_id = $operator->id;
            if ($lockedTicket->status === TicketStatus::OPEN) {
                $lockedTicket->status = TicketStatus::IN_PROGRESS;
            }
            $lockedTicket->save();

            return $lockedTicket;
        });
    }

    /**
     * Menambahkan balasan pada tiket.
     * Jika ini balasan pertama dari operator manusia pada tiket OPEN:
     * - Status otomatis menjadi IN_PROGRESS.
     * - operator_id otomatis terisi jika belum ada operator.
     * - first_response_at dicatat.
     * Jika $isAuto = true, first_response_at TIDAK dicatat.
     *
     * @param HelpdeskTicket $ticket
     * @param User|null $author
     * @param string $message
     * @param bool $isAuto
     * @param CarbonInterface|null $now
     * @return TicketResponse
     * @throws DomainException Jika tiket sudah CLOSED (terminal state).
     */
    public function addReply(
        HelpdeskTicket $ticket,
        ?User $author,
        string $message,
        bool $isAuto = false,
        ?CarbonInterface $now = null
    ): TicketResponse {
        $current = $now ? Carbon::instance($now) : Carbon::now();

        return DB::transaction(function () use ($ticket, $author, $message, $isAuto, $current) {
            $lockedTicket = HelpdeskTicket::where('id', $ticket->id)->lockForUpdate()->firstOrFail();

            if ($lockedTicket->isClosed()) {
                throw new DomainException('Tiket yang sudah ditutup tidak dapat menerima balasan.');
            }

            $responderId = $author?->id ?? User::where('role', User::ROLE_ADMIN)->value('id') ?? $lockedTicket->reporter_id;

            $response = TicketResponse::create([
                'ticket_id' => $lockedTicket->id,
                'responder_id' => $responderId,
                'message' => $message,
                'is_auto_reply' => $isAuto,
                'created_at' => $current,
            ]);

            // Jika balasan berasal dari operator manusia (bukan bot/auto)
            $isHumanOperator = $author && ($author->isOperator() || $author->isAdmin()) && ! $isAuto;

            if ($isHumanOperator) {
                // Catat first_response_at jika belum pernah ada
                if ($lockedTicket->first_response_at === null) {
                    $lockedTicket->first_response_at = $current;
                }

                // Jika status masih OPEN, ubah ke IN_PROGRESS
                if ($lockedTicket->status === TicketStatus::OPEN) {
                    $lockedTicket->status = TicketStatus::IN_PROGRESS;
                }

                // Jika operator belum di-assign, assign ke operator yang menjawab
                if ($lockedTicket->operator_id === null) {
                    $lockedTicket->operator_id = $author->id;
                }

                $lockedTicket->save();
            }

            return $response;
        });
    }

    /**
     * Mengubah status tiket. Menjadi SATU-SATUNYA tempat penulisan closed_at dan resolution_time.
     * Rumus: max(0, (int) $ticket->created_at->diffInMinutes($closedAt)).
     *
     * @param HelpdeskTicket $ticket
     * @param TicketStatus $newStatus
     * @param User $actor
     * @param string|null $solutionSummary
     * @param CarbonInterface|null $now
     * @return HelpdeskTicket
     * @throws DomainException Jika status tiket sudah CLOSED atau transisi ilegal.
     * @throws AuthorizationException
     */
    public function changeStatus(
        HelpdeskTicket $ticket,
        TicketStatus $newStatus,
        User $actor,
        ?string $solutionSummary = null,
        ?CarbonInterface $now = null
    ): HelpdeskTicket {
        if (! $actor->isOperator() && ! $actor->isAdmin()) {
            throw new AuthorizationException('Hanya staf yang berwenang mengubah status tiket.');
        }

        $current = $now ? Carbon::instance($now) : Carbon::now();

        return DB::transaction(function () use ($ticket, $newStatus, $actor, $solutionSummary, $current) {
            $lockedTicket = HelpdeskTicket::where('id', $ticket->id)->lockForUpdate()->firstOrFail();

            if ($lockedTicket->isClosed()) {
                throw new DomainException('Tiket yang sudah ditutup tidak dapat diubah statusnya lagi.');
            }

            if ($lockedTicket->status === $newStatus) {
                throw new DomainException('Tiket sudah berstatus ' . $newStatus->value . '.');
            }

            if ($lockedTicket->status === TicketStatus::IN_PROGRESS && $newStatus === TicketStatus::OPEN) {
                throw new DomainException('Tiket In-Progress tidak dapat dikembalikan ke status Open.');
            }

            if ($newStatus === TicketStatus::CLOSED) {
                $closedAt = $current;
                $diff = (int) $lockedTicket->created_at->diffInMinutes($closedAt);
                $resolutionTime = max(0, $diff);

                $lockedTicket->status = TicketStatus::CLOSED;
                $lockedTicket->closed_at = $closedAt;
                $lockedTicket->resolution_time = $resolutionTime;

                if ($solutionSummary !== null && trim($solutionSummary) !== '') {
                    $lockedTicket->solution_summary = trim($solutionSummary);
                }

                $lockedTicket->save();

                return $lockedTicket;
            }

            $lockedTicket->status = $newStatus;
            if ($solutionSummary !== null && trim($solutionSummary) !== '') {
                $lockedTicket->solution_summary = trim($solutionSummary);
            }
            $lockedTicket->save();

            return $lockedTicket;
        });
    }
}
