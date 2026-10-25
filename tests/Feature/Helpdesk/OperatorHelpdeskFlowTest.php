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

    public function test_operator_can_merge_duplicate_from_ticket_detail(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $target = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN, 'subject' => 'Target Ticket']);
        $duplicate = HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN, 'subject' => 'Duplicate Candidate']);

        Livewire::actingAs($operator)
            ->test(OperatorTicketDetail::class, ['ticketId' => $target->id])
            ->assertSee('Mungkin Duplikat')
            ->call('mergeDuplicate', $duplicate->id)
            ->assertSee('berhasil digabungkan');

        $duplicate->refresh();
        $this->assertSame(TicketStatus::CLOSED, $duplicate->status);
        $this->assertSame($target->id, $duplicate->duplicate_of_id);
        $this->assertNotNull($duplicate->closed_at);
        $this->assertNotNull($duplicate->resolution_time);
    }

    public function test_operator_queue_page_contains_no_peserta_dashboard_link_and_shows_no_error_flash(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        $response = $this->actingAs($operator)->get('/helpdesk');

        $response->assertOk();
        $response->assertSessionMissing('error');
        $response->assertDontSee('Halaman ini hanya untuk peran peserta');
        $response->assertDontSee(route('dashboard'));
        $response->assertDontSee('/dashboard');
        $response->assertDontSee('Portal Rider');
        $response->assertSee('Antrian tiket');
        $response->assertSee('Keluar Akun');
    }

    public function test_operator_ticket_detail_page_contains_no_peserta_dashboard_link(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $ticket = HelpdeskTicket::factory()->create();

        $response = $this->actingAs($operator)->get("/helpdesk/tickets/{$ticket->id}");

        $response->assertOk();
        $response->assertSessionMissing('error');
        $response->assertDontSee('Halaman ini hanya untuk peran peserta');
        $response->assertDontSee(route('dashboard'));
        $response->assertDontSee('/dashboard');
        $response->assertDontSee('Portal Rider');
        $response->assertSee('Antrian tiket');
        $response->assertSee('Keluar Akun');
    }

    public function test_operator_sidebar_follows_mockup_with_single_antrian_tiket_item(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        HelpdeskTicket::factory()->create(['status' => TicketStatus::OPEN]);

        $response = $this->actingAs($operator)->get('/helpdesk');
        $content = $response->getContent();

        // Extract aside HTML
        $this->assertMatchesRegularExpression('/<aside[^>]*>.*?<\/aside>/s', $content);
        preg_match('/<aside[^>]*>(.*?)<\/aside>/s', $content, $matches);
        $asideHtml = $matches[1] ?? '';

        // Aside contains single "Antrian tiket" nav item and logout
        $this->assertStringContainsString('Antrian tiket', $asideHtml);
        $this->assertStringContainsString('Keluar Akun', $asideHtml);
        $this->assertStringNotContainsString('Portal Rider', $asideHtml);

        // Sidebar does NOT contain the duplicated filter items
        $this->assertStringNotContainsString('Tiket Baru (Open)', $asideHtml);
        $this->assertStringNotContainsString('Dalam Proses', $asideHtml);
        $this->assertStringNotContainsString('Tiket Saya', $asideHtml);
        $this->assertStringNotContainsString('Selesai (Closed)', $asideHtml);

        // Page body (main) DOES contain the filter tabs
        $this->assertStringContainsString('Semua Antrian', $content);
        $this->assertStringContainsString('Baru (Open)', $content);
    }

    public function test_admin_retains_link_to_admin_panel_on_operator_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get('/helpdesk');

        $response->assertOk();
        $response->assertSee('/admin');
        $response->assertSee('Admin Panel');
        $response->assertDontSee('Portal Rider');
    }

    public function test_mizu_chat_widget_is_not_rendered_on_helpdesk_pages_or_for_operator_and_admin(): void
    {
        config(['services.gemini.key' => 'fake-gemini-key']);

        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $ticket = HelpdeskTicket::factory()->create();

        // 1. Guest on home page / DOES render widget
        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="Tanya Mizu"', false);

        // 2. Peserta on home page / DOES render widget
        $this->actingAs($peserta)->get('/')
            ->assertOk()
            ->assertSee('aria-label="Tanya Mizu"', false);

        // 3. Operator on /helpdesk -> no widget
        $this->actingAs($operator)->get('/helpdesk')
            ->assertOk()
            ->assertDontSee('aria-label="Tanya Mizu"', false)
            ->assertDontSee('mizu-chat-widget');

        // 4. Operator on /helpdesk/tickets/{id} -> no widget
        $this->actingAs($operator)->get("/helpdesk/tickets/{$ticket->id}")
            ->assertOk()
            ->assertDontSee('aria-label="Tanya Mizu"', false)
            ->assertDontSee('mizu-chat-widget');

        // 5. Operator on home page / -> no widget
        $this->actingAs($operator)->get('/')
            ->assertOk()
            ->assertDontSee('aria-label="Tanya Mizu"', false)
            ->assertDontSee('mizu-chat-widget');

        // 6. Admin on /helpdesk -> no widget
        $this->actingAs($admin)->get('/helpdesk')
            ->assertOk()
            ->assertDontSee('aria-label="Tanya Mizu"', false)
            ->assertDontSee('mizu-chat-widget');

        // 7. Admin on home page / -> no widget
        $this->actingAs($admin)->get('/')
            ->assertOk()
            ->assertDontSee('aria-label="Tanya Mizu"', false)
            ->assertDontSee('mizu-chat-widget');
    }

    public function test_operator_queue_orders_tickets_by_active_then_priority_then_fifo(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        // Fixed fixtures: old Low, new High, old High, Mid, plus a closed High
        $oldLow = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::LOW,
            'created_at' => Carbon::parse('2026-10-25 08:00:00'),
            'subject' => 'Old Low Ticket',
        ]);
        $newHigh = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::HIGH,
            'created_at' => Carbon::parse('2026-10-25 09:30:00'),
            'subject' => 'New High Ticket',
        ]);
        $oldHigh = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::HIGH,
            'created_at' => Carbon::parse('2026-10-25 09:00:00'),
            'subject' => 'Old High Ticket',
        ]);
        $mid = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::MID,
            'created_at' => Carbon::parse('2026-10-25 09:15:00'),
            'subject' => 'Mid Ticket',
        ]);
        $closedHigh = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::CLOSED,
            'priority' => TicketPriority::HIGH,
            'created_at' => Carbon::parse('2026-10-25 07:00:00'),
            'subject' => 'Closed High Ticket',
        ]);

        $rendered = Livewire::actingAs($operator)
            ->test(OperatorQueue::class)
            ->viewData('tickets');

        $ids = collect($rendered->items())->pluck('id')->all();

        // Exact expected order:
        // 1. Old High (High, 09:00 FIFO)
        // 2. New High (High, 09:30)
        // 3. Mid (Mid, 09:15)
        // 4. Old Low (Low, 08:00)
        // 5. Closed High (Closed comes after active)
        $this->assertSame([$oldHigh->id, $newHigh->id, $mid->id, $oldLow->id, $closedHigh->id], $ids);
    }

    public function test_operator_queue_priority_filter_filters_tickets(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        $high = HelpdeskTicket::factory()->create([
            'priority' => TicketPriority::HIGH,
            'subject' => 'Target High Issue',
        ]);
        $low = HelpdeskTicket::factory()->create([
            'priority' => TicketPriority::LOW,
            'subject' => 'Target Low Issue',
        ]);

        Livewire::actingAs($operator)
            ->test(OperatorQueue::class)
            ->assertSee('Target High Issue')
            ->assertSee('Target Low Issue')
            ->set('priorityFilter', 'High')
            ->assertSee('Target High Issue')
            ->assertDontSee('Target Low Issue')
            ->set('priorityFilter', 'Low')
            ->assertDontSee('Target High Issue')
            ->assertSee('Target Low Issue');
    }
}

