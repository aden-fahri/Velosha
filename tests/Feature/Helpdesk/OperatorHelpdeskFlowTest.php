<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Livewire\Helpdesk\OperatorQueue;
use App\Livewire\Helpdesk\OperatorTicketDetail;
use App\Models\HelpdeskTicket;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OperatorHelpdeskFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->get('/helpdesk')->assertRedirect('/login');
        $this->get("/helpdesk/tickets/{$ticket->id}")->assertRedirect('/login');
    }

    public function test_peserta_is_forbidden_and_bounced_from_operator_area(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $ticket = HelpdeskTicket::factory()->create();

        // 1. HTTP Route via middleware role: dipantulkan ke home() peserta dengan pesan error
        $this->actingAs($peserta)->get('/helpdesk')
            ->assertRedirect($peserta->home())
            ->assertSessionHas('error');

        $this->actingAs($peserta)->get("/helpdesk/tickets/{$ticket->id}")
            ->assertRedirect($peserta->home())
            ->assertSessionHas('error');

        // 2. Direct Livewire mount: melempar abort(403)
        Livewire::actingAs($peserta)
            ->test(OperatorQueue::class)
            ->assertForbidden();

        Livewire::actingAs($peserta)
            ->test(OperatorTicketDetail::class, ['ticketId' => $ticket->id])
            ->assertForbidden();
    }

    public function test_operator_and_admin_can_access_queue_page(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($operator)->get('/helpdesk')->assertOk();
        $this->actingAs($admin)->get('/helpdesk')->assertOk();
    }

    public function test_operator_queue_lists_tickets_and_filters_correctly(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticketOpen = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN, 'subject' => 'Tiket Masalah Satu']);
        $ticketClosed = HelpdeskTicket::factory()->create(['status' => TicketStatus::CLOSED, 'subject' => 'Tiket Masalah Selesai']);

        Livewire::actingAs($operator)
            ->test(OperatorQueue::class)
            ->assertSee('Tiket Masalah Satu')
            ->assertSee('Tiket Masalah Selesai')
            ->set('filter', 'open')
            ->assertSee('Tiket Masalah Satu')
            ->assertDontSee('Tiket Masalah Selesai');
    }

    public function test_operator_can_claim_unassigned_open_ticket(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'operator_id' => null,
        ]);

        Livewire::actingAs($operator)
            ->test(OperatorQueue::class)
            ->call('claimTicket', $ticket->id)
            ->assertSee('berhasil diklaim');

        $ticket->refresh();
        $this->assertSame($operator->id, $ticket->operator_id);
        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->status);
    }

    public function test_second_operator_claim_fails_on_already_claimed_ticket(): void
    {
        $operator1 = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $operator2 = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::IN_PROGRESS,
            'operator_id' => $operator1->id,
        ]);

        Livewire::actingAs($operator2)
            ->test(OperatorQueue::class)
            ->call('claimTicket', $ticket->id)
            ->assertSee('sudah diklaim oleh operator lain');

        $ticket->refresh();
        $this->assertSame($operator1->id, $ticket->operator_id);
    }

    public function test_operator_ticket_detail_renders_and_allows_first_reply(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'operator_id' => null,
            'first_response_at' => null,
        ]);

        Livewire::actingAs($operator)
            ->test(OperatorTicketDetail::class, ['ticketId' => $ticket->id])
            ->set('replyMessage', 'Halo peserta, saya sedang memeriksa kendala RFID Anda.')
            ->call('sendReply')
            ->assertHasNoErrors()
            ->assertSee('Halo peserta, saya sedang memeriksa kendala RFID Anda.');

        $ticket->refresh();
        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->status);
        $this->assertSame($operator->id, $ticket->operator_id);
        $this->assertNotNull($ticket->first_response_at);
    }

    public function test_operator_can_close_ticket_with_solution_summary(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::IN_PROGRESS,
            'operator_id' => $operator->id,
            'created_at' => Carbon::now()->subMinutes(90),
        ]);

        Livewire::actingAs($operator)
            ->test(OperatorTicketDetail::class, ['ticketId' => $ticket->id])
            ->set('solutionSummary', 'Nomor BIB RFID telah disinkronkan ulang.')
            ->set('isReference', true)
            ->call('closeTicket')
            ->assertSee('berhasil ditutup');

        $ticket->refresh();
        $this->assertSame(TicketStatus::CLOSED, $ticket->status);
        $this->assertNotNull($ticket->closed_at);
        $this->assertSame(90, $ticket->resolution_time);
        $this->assertSame('Nomor BIB RFID telah disinkronkan ulang.', $ticket->solution_summary);
        $this->assertTrue($ticket->is_reference);
    }

    public function test_closed_ticket_rejects_further_replies(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::CLOSED,
            'closed_at' => Carbon::now(),
        ]);

        Livewire::actingAs($operator)
            ->test(OperatorTicketDetail::class, ['ticketId' => $ticket->id])
            ->set('replyMessage', 'Mencoba membalas tiket closed.')
            ->call('sendReply')
            ->assertSee('ditutup');
    }

    public function test_operator_can_reassign_ticket_to_another_operator(): void
    {
        $operator1 = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $operator2 = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::IN_PROGRESS,
            'operator_id' => $operator1->id,
        ]);

        Livewire::actingAs($operator1)
            ->test(OperatorTicketDetail::class, ['ticketId' => $ticket->id])
            ->set('assignOperatorId', $operator2->id)
            ->call('updateAssignment')
            ->assertSee('berhasil ditugaskan');

        $ticket->refresh();
        $this->assertSame($operator2->id, $ticket->operator_id);
    }
}
