<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\SatisfactionRating;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReporterHelpdeskFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pastikan halaman Bantuan publik memuat kartu CTA Buat Tiket Bantuan.
     */
    public function test_public_help_page_displays_create_ticket_cta(): void
    {
        $this->get('/help')
            ->assertOk()
            ->assertSee('Belum ketemu jawabannya? Buat tiket bantuan')
            ->assertSee('/dashboard/bantuan/buat');
    }

    /**
     * Pastikan tamu yang membuka /dashboard/bantuan dialihkan ke login.
     */
    public function test_guest_is_redirected_to_login_when_accessing_helpdesk(): void
    {
        $this->get('/dashboard/bantuan')
            ->assertRedirect('/login');

        $this->get('/dashboard/bantuan/buat')
            ->assertRedirect('/login');
    }

    /**
     * Pastikan pelapor (peserta) dapat membuat tiket baru dengan validasi dan SLA due date.
     */
    public function test_reporter_can_create_ticket(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('subject', 'E-ticket belum muncul setelah bayar QRIS')
            ->set('description', 'Saya sudah membayar lewat QRIS tadi pagi tapi tiket belum masuk ke tab BIB.')
            ->set('priority', 'High')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas(HelpdeskTicket::TABLE_NAME, [
            'reporter_id' => $peserta->id,
            'subject' => 'E-ticket belum muncul setelah bayar QRIS',
            'priority' => TicketPriority::HIGH->value,
            'status' => TicketStatus::OPEN->value,
        ]);

        $ticket = HelpdeskTicket::where('subject', 'E-ticket belum muncul setelah bayar QRIS')->first();
        $this->assertNotNull($ticket->response_due_at);
        $this->assertNotNull($ticket->resolution_due_at);
    }

    /**
     * Validasi input tiket bantuan (subjek minimal 5, deskripsi minimal 10).
     */
    public function test_create_ticket_validates_inputs(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('subject', 'Halo')
            ->set('description', 'Pendek')
            ->call('submit')
            ->assertHasErrors(['subject', 'description']);
    }

    /**
     * ISOLASI PELAPOR: Pelapor hanya dapat melihat tiket miliknya.
     * Mengakses ID tiket orang lain harus mengembalikan 404.
     */
    public function test_reporter_isolation_other_users_ticket_returns_404(): void
    {
        $peserta1 = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $peserta2 = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $ticketPeserta1 = HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta1->id,
            'subject' => 'Kendala Rahasia Peserta 1',
        ]);

        // Peserta 2 mencoba membuka tiket milik Peserta 1 -> harus 404
        $this->actingAs($peserta2)
            ->get('/dashboard/bantuan/' . $ticketPeserta1->id)
            ->assertNotFound();

        // Peserta 2 mencoba memilih tiket via Livewire component -> harus 404
        Livewire::actingAs($peserta2)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticketPeserta1->id])
            ->assertNotFound();
    }

    /**
     * Pelapor dapat mengirim balasan ke tiket miliknya saat status masih Open/In-Progress.
     */
    public function test_reporter_can_reply_to_own_open_ticket(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'status' => TicketStatus::OPEN,
        ]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->set('replyMessage', 'Ini bukti tangkapan layar pembayaran saya.')
            ->call('sendReply')
            ->assertHasNoErrors()
            ->assertDispatched('ticket-replied');

        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $ticket->id,
            'responder_id' => $peserta->id,
            'message' => 'Ini bukti tangkapan layar pembayaran saya.',
            'is_auto_reply' => false,
        ]);
    }

    /**
     * Pelapor TIDAK BISA membalas tiket yang berstatus Closed.
     */
    public function test_reporter_cannot_reply_to_closed_ticket(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $ticket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
        ]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->set('replyMessage', 'Tolong jangan ditutup dulu!')
            ->call('sendReply')
            ->assertHasErrors(['replyMessage']);

        $this->assertDatabaseMissing('ticket_responses', [
            'ticket_id' => $ticket->id,
            'message' => 'Tolong jangan ditutup dulu!',
        ]);
    }

    /**
     * Pelapor dapat memberi rating setelah Closed (hanya satu kali).
     */
    public function test_reporter_can_rate_closed_ticket_once(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $ticket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
        ]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->set('ratingScore', 5)
            ->set('ratingFeedback', 'Respon pengelola sangat cepat dan solutif.')
            ->call('submitRating')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('satisfaction_ratings', [
            'ticket_id' => $ticket->id,
            'score' => 5,
            'feedback' => 'Respon pengelola sangat cepat dan solutif.',
        ]);

        // Coba submit rating kedua kali -> harus ditolak
        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->set('ratingScore', 3)
            ->call('submitRating')
            ->assertHasErrors(['ratingFeedback']);
    }

    /**
     * Sidebar Portal Rider menampilkan item Tiket Bantuan dan lencana jumlah tiket aktif.
     */
    public function test_portal_dashboard_sidebar_has_helpdesk_ticket_link(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'status' => TicketStatus::OPEN,
        ]);

        $this->actingAs($peserta)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tiket Bantuan')
            ->assertSee('/dashboard/bantuan');
    }
}
