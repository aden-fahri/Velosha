<?php

namespace Tests\Unit\Helpdesk;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\Helpdesk\TicketService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    private TicketService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TicketService();
    }

    public function test_create_ticket_generates_ticket_with_sla_due_dates(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $now = Carbon::parse('2026-10-25 10:00:00');

        $ticket = $this->service->createTicket($reporter, [
            'subject' => 'Kendala E-Ticket',
            'description' => 'E-ticket belum keluar di portal',
            'priority' => TicketPriority::HIGH,
        ], $now);

        $this->assertNotNull($ticket->id);
        $this->assertGreaterThan(0, $ticket->id);
        $this->assertSame(TicketStatus::OPEN, $ticket->status);
        $this->assertSame($reporter->id, $ticket->reporter_id);
        $this->assertNull($ticket->operator_id);
        $this->assertNull($ticket->first_response_at);
        $this->assertSame('2026-10-25 10:15:00', $ticket->response_due_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-25 14:00:00', $ticket->resolution_due_at->format('Y-m-d H:i:s'));
    }

    public function test_resolution_time_calculation_exact_minutes_and_signed_clamp(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Kasus 1: Tiket dibuat 10:00, ditutup 12:30 -> harus tepat 150 menit
        $createdAt = Carbon::parse('2026-10-25 10:00:00');
        $closedAt = Carbon::parse('2026-10-25 12:30:00');

        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'status' => TicketStatus::IN_PROGRESS,
            'created_at' => $createdAt,
            'closed_at' => null,
            'resolution_time' => null,
        ]);

        $closedTicket = $this->service->changeStatus(
            $ticket,
            TicketStatus::CLOSED,
            $operator,
            'Solusi masalah sinkronisasi akun.',
            $closedAt
        );

        $this->assertSame(150, $closedTicket->resolution_time);
        $this->assertSame(TicketStatus::CLOSED, $closedTicket->status);
        $this->assertSame('Solusi masalah sinkronisasi akun.', $closedTicket->solution_summary);

        // Kasus 2: ClosedAt sebelum createdAt (clock skew) -> max(0, negative) harus menghasilkan 0
        $ticketSkew = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'status' => TicketStatus::IN_PROGRESS,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
        ]);

        $closedBeforeCreated = Carbon::parse('2026-10-25 09:30:00');
        $resultSkew = $this->service->changeStatus(
            $ticketSkew,
            TicketStatus::CLOSED,
            $operator,
            'Closed sebelum created',
            $closedBeforeCreated
        );

        $this->assertSame(0, $resultSkew->resolution_time);
    }

    public function test_atomic_claim_ticket_and_concurrency_protection(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator1 = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $operator2 = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'operator_id' => null,
            'status' => TicketStatus::OPEN,
        ]);

        // Operator 1 berhasil klaim
        $claimed = $this->service->claimTicket($ticket, $operator1);
        $this->assertSame($operator1->id, $claimed->operator_id);
        $this->assertSame(TicketStatus::IN_PROGRESS, $claimed->status);

        // Operator 2 mencoba klaim tiket yang sudah diklaim -> harus melempar DomainException
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tiket sudah diklaim oleh operator lain.');

        $this->service->claimTicket($ticket->fresh(), $operator2);
    }

    public function test_non_operator_cannot_claim_ticket(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $ticket = HelpdeskTicket::factory()->create(['operator_id' => null]);

        $this->expectException(AuthorizationException::class);
        $this->service->claimTicket($ticket, $peserta);
    }

    public function test_first_human_operator_reply_transitions_to_in_progress_and_records_first_response(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'operator_id' => null,
            'status' => TicketStatus::OPEN,
            'first_response_at' => null,
        ]);

        $firstResponseTime = Carbon::parse('2026-10-25 10:10:00');

        $reply = $this->service->addReply(
            $ticket,
            $operator,
            'Halo, kami sedang memeriksa kendala nomor BIB Anda.',
            false,
            $firstResponseTime
        );

        $ticket->refresh();

        $this->assertSame($operator->id, $ticket->operator_id);
        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->status);
        $this->assertNotNull($ticket->first_response_at);
        $this->assertSame('2026-10-25 10:10:00', $ticket->first_response_at->format('Y-m-d H:i:s'));

        // Balasan kedua dari operator tidak boleh menimpa first_response_at
        $laterTime = Carbon::parse('2026-10-25 10:20:00');
        $this->service->addReply($ticket, $operator, 'Update informasi tambahan.', false, $laterTime);

        $ticket->refresh();
        $this->assertSame('2026-10-25 10:10:00', $ticket->first_response_at->format('Y-m-d H:i:s'));
    }

    public function test_bot_auto_reply_does_not_trigger_first_response_at(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'operator_id' => null,
            'status' => TicketStatus::OPEN,
            'first_response_at' => null,
        ]);

        // Balasan otomatis sistem
        $this->service->addReply(
            $ticket,
            null,
            'Tiket Anda telah tercatat pada sistem antrean.',
            true
        );

        $ticket->refresh();
        $this->assertSame(TicketStatus::OPEN, $ticket->status);
        $this->assertNull($ticket->first_response_at);
        $this->assertNull($ticket->operator_id);
    }

    public function test_closed_ticket_is_terminal_and_rejects_all_mutations(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::CLOSED,
            'closed_at' => Carbon::now(),
        ]);

        // 1. Ditolak saat klaim
        try {
            $this->service->claimTicket($ticket, $operator);
            $this->fail('Harusnya melempar DomainException');
        } catch (DomainException $e) {
            $this->assertStringContainsString('ditutup', $e->getMessage());
        }

        // 2. Ditolak saat balas
        try {
            $this->service->addReply($ticket, $operator, 'Balasan baru');
            $this->fail('Harusnya melempar DomainException');
        } catch (DomainException $e) {
            $this->assertStringContainsString('ditutup', $e->getMessage());
        }

        // 3. Ditolak saat assign
        try {
            $this->service->assignOperator($ticket, $operator, $operator);
            $this->fail('Harusnya melempar DomainException');
        } catch (DomainException $e) {
            $this->assertStringContainsString('ditutup', $e->getMessage());
        }

        // 4. Ditolak saat ubah status
        try {
            $this->service->changeStatus($ticket, TicketStatus::IN_PROGRESS, $operator);
            $this->fail('Harusnya melempar DomainException');
        } catch (DomainException $e) {
            $this->assertStringContainsString('ditutup', $e->getMessage());
        }
    }
}
