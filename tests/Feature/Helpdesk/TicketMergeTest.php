<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\Helpdesk\TicketService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @file TicketMergeTest.php
 * @brief Pengujian menyeluruh untuk fungsi mergeTickets() pada TicketService.
 */
class TicketMergeTest extends TestCase
{
    use RefreshDatabase;

    private TicketService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TicketService::class);
    }

    /**
     * Operator berhasil menggabungkan tiket duplikat:
     * - Tiket duplikat ditutup (Closed) melalui changeStatus().
     * - Kolom duplicate_of_id merujuk ke tiket target.
     * - closed_at dan resolution_time terhitung otomatis.
     * - Balasan penaut diposting melalui addReply() sebagai balasan manusia (first_response_at terisi jika sebelumnya null).
     */
    public function test_operator_can_merge_duplicate_ticket_successfully(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $reporterA = User::factory()->create();
        $reporterB = User::factory()->create();

        $createdAt = Carbon::parse('2026-10-25 08:00:00');
        $target = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporterA->id,
            'status' => TicketStatus::OPEN,
            'created_at' => $createdAt,
        ]);

        $duplicate = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporterB->id,
            'status' => TicketStatus::OPEN,
            'created_at' => $createdAt,
            'first_response_at' => null,
            'closed_at' => null,
            'resolution_time' => null,
            'duplicate_of_id' => null,
        ]);

        $mergeTime = Carbon::parse('2026-10-25 09:30:00'); // 90 menit setelah created_at

        $merged = $this->service->mergeTickets($target, $duplicate, $operator, $mergeTime);

        // Verifikasi tiket duplikat
        $this->assertSame(TicketStatus::CLOSED, $merged->status);
        $this->assertSame($target->id, $merged->duplicate_of_id);
        $this->assertSame(90, $merged->resolution_time);
        $this->assertEquals($mergeTime->toDateTimeString(), $merged->closed_at->toDateTimeString());
        $this->assertEquals($mergeTime->toDateTimeString(), $merged->first_response_at->toDateTimeString());

        // Verifikasi balasan penaut
        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $duplicate->id,
            'responder_id' => $operator->id,
            'is_auto_reply' => false,
        ]);

        $latestResponse = $merged->responses()->latest('id')->first();
        $this->assertNotNull($latestResponse);
        $this->assertStringContainsString((string) $target->id, $latestResponse->message);

        // Verifikasi tiket target tetap Open dan tidak berubah status
        $this->assertSame(TicketStatus::OPEN, $target->fresh()->status);
        $this->assertNull($target->fresh()->duplicate_of_id);
    }

    /**
     * Admin juga berwenang melakukan penggabungan tiket duplikat.
     */
    public function test_admin_can_merge_duplicate_ticket(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $target = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $duplicate = HelpdeskTicket::factory()->create(['status' => TicketStatus::IN_PROGRESS]);

        $merged = $this->service->mergeTickets($target, $duplicate, $admin);

        $this->assertSame(TicketStatus::CLOSED, $merged->status);
        $this->assertSame($target->id, $merged->duplicate_of_id);
    }

    /**
     * Ditolak: tiket tidak boleh digabungkan ke dirinya sendiri.
     */
    public function test_merge_rejects_merging_into_itself(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tiket tidak dapat digabungkan ke dirinya sendiri.');

        $this->service->mergeTickets($ticket, $ticket, $operator);
    }

    /**
     * Ditolak: tiket target tidak boleh merupakan tiket yang sudah berstatus duplikat dari tiket lain.
     */
    public function test_merge_rejects_target_that_is_already_a_duplicate(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        $masterTicket = HelpdeskTicket::factory()->create();
        $targetDuplicate = HelpdeskTicket::factory()->create([
            'duplicate_of_id' => $masterTicket->id,
            'status' => TicketStatus::OPEN,
        ]);

        $duplicate = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tiket target tidak boleh merupakan tiket duplikat.');

        $this->service->mergeTickets($targetDuplicate, $duplicate, $operator);
    }

    /**
     * Ditolak: tiket duplikat yang sudah memiliki duplicate_of_id tidak boleh digabungkan ulang (no re-merge).
     */
    public function test_merge_rejects_duplicate_that_is_already_a_duplicate(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $target = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);

        $firstMaster = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $alreadyMergedDuplicate = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'duplicate_of_id' => $firstMaster->id,
        ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tiket duplikat tidak boleh merupakan tiket yang sudah digabungkan.');

        $this->service->mergeTickets($target, $alreadyMergedDuplicate, $operator);
    }

    /**
     * Ditolak: tiket yang sudah berstatus Closed tidak boleh digabungkan sebagai duplikat.
     */
    public function test_merge_rejects_duplicate_that_is_already_closed(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $target = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $alreadyClosed = HelpdeskTicket::factory()->closed()->create();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tiket yang sudah ditutup tidak dapat digabungkan.');

        $this->service->mergeTickets($target, $alreadyClosed, $operator);
    }

    /**
     * Ditolak: pengguna dengan role selain operator/admin (misal: peserta) tidak berwenang.
     */
    public function test_merge_rejects_unauthorized_actor(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $target = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $duplicate = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);

        $this->expectException(AuthorizationException::class);

        $this->service->mergeTickets($target, $duplicate, $peserta);
    }

    /**
     * Penguncian urutan ID menaik (ascending order):
     * Harus bekerja dengan lancar baik ketika target->id < duplicate->id maupun target->id > duplicate->id.
     */
    public function test_merge_locks_both_rows_in_ascending_id_order_regardless_of_parameter_order(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        // Skenario A: target (ID lebih kecil) dan duplicate (ID lebih besar)
        $targetA = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $duplicateA = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $this->assertLessThan($duplicateA->id, $targetA->id);

        $mergedA = $this->service->mergeTickets($targetA, $duplicateA, $operator);
        $this->assertSame(TicketStatus::CLOSED, $mergedA->status);
        $this->assertSame($targetA->id, $mergedA->duplicate_of_id);

        // Skenario B: target (ID lebih besar) dan duplicate (ID lebih kecil)
        $duplicateB = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $targetB = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);
        $this->assertGreaterThan($duplicateB->id, $targetB->id);

        $mergedB = $this->service->mergeTickets($targetB, $duplicateB, $operator);
        $this->assertSame(TicketStatus::CLOSED, $mergedB->status);
        $this->assertSame($targetB->id, $mergedB->duplicate_of_id);
    }
}
