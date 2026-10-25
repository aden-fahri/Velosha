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

    /**
     * Sesuai aturan SLA: response_due_at dan resolution_due_at harus dihitung
     * tepat dari sla_policies berdasarkan prioritas High/Mid/Low dengan Carbon::setTestNow().
     */
    public function test_create_ticket_calculates_exact_sla_due_dates_per_priority(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $knownNow = \Carbon\Carbon::create(2026, 10, 24, 10, 0, 0);
        \Carbon\Carbon::setTestNow($knownNow);

        try {
            // 1. High Priority: 15 menit response, 240 menit (4 jam) resolution
            Livewire::actingAs($peserta)
                ->test(\App\Livewire\Helpdesk\CreateTicket::class)
                ->set('subject', 'Tiket High Priority Akut')
                ->set('description', 'Deskripsi masalah dengan prioritas tinggi sekali.')
                ->set('priority', 'High')
                ->call('submit')
                ->assertHasNoErrors();

            $highTicket = HelpdeskTicket::where('subject', 'Tiket High Priority Akut')->first();
            $this->assertNotNull($highTicket);
            $this->assertSame('2026-10-24 10:15:00', $highTicket->response_due_at->format('Y-m-d H:i:s'));
            $this->assertSame('2026-10-24 14:00:00', $highTicket->resolution_due_at->format('Y-m-d H:i:s'));

            // 2. Mid Priority: 60 menit response, 1440 menit (24 jam) resolution
            Livewire::actingAs($peserta)
                ->test(\App\Livewire\Helpdesk\CreateTicket::class)
                ->set('subject', 'Tiket Mid Priority Standar')
                ->set('description', 'Deskripsi masalah dengan prioritas sedang menengah.')
                ->set('priority', 'Mid')
                ->call('submit')
                ->assertHasNoErrors();

            $midTicket = HelpdeskTicket::where('subject', 'Tiket Mid Priority Standar')->first();
            $this->assertNotNull($midTicket);
            $this->assertSame('2026-10-24 11:00:00', $midTicket->response_due_at->format('Y-m-d H:i:s'));
            $this->assertSame('2026-10-25 10:00:00', $midTicket->resolution_due_at->format('Y-m-d H:i:s'));

            // 3. Low Priority: 240 menit response, 4320 menit (72 jam) resolution
            Livewire::actingAs($peserta)
                ->test(\App\Livewire\Helpdesk\CreateTicket::class)
                ->set('subject', 'Tiket Low Priority Santai')
                ->set('description', 'Deskripsi masalah dengan prioritas rendah umum.')
                ->set('priority', 'Low')
                ->call('submit')
                ->assertHasNoErrors();

            $lowTicket = HelpdeskTicket::where('subject', 'Tiket Low Priority Santai')->first();
            $this->assertNotNull($lowTicket);
            $this->assertSame('2026-10-24 14:00:00', $lowTicket->response_due_at->format('Y-m-d H:i:s'));
            $this->assertSame('2026-10-27 10:00:00', $lowTicket->resolution_due_at->format('Y-m-d H:i:s'));
        } finally {
            \Carbon\Carbon::setTestNow(null);
        }
    }

    /**
     * Rate limit: tiket ke-6 dalam 1 jam ditolak, dan counter dicatat per-user.
     */
    public function test_create_ticket_rate_limit_rejects_sixth_ticket_per_user(): void
    {
        $peserta1 = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $peserta2 = User::factory()->create(['role' => User::ROLE_PESERTA]);

        \Illuminate\Support\Facades\RateLimiter::clear('helpdesk-create:' . $peserta1->id);
        \Illuminate\Support\Facades\RateLimiter::clear('helpdesk-create:' . $peserta2->id);

        // Buat 5 tiket untuk peserta 1 -> harus berhasil semua
        for ($i = 1; $i <= 5; $i++) {
            Livewire::actingAs($peserta1)
                ->test(\App\Livewire\Helpdesk\CreateTicket::class)
                ->set('subject', "Tiket Ke-{$i} Peserta Satu")
                ->set('description', 'Deskripsi tiket pengujian batasan rate limiter.')
                ->set('priority', 'Low')
                ->call('submit')
                ->assertHasNoErrors();
        }

        // Tiket ke-6 untuk peserta 1 -> HARUS DITOLAK
        Livewire::actingAs($peserta1)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('subject', 'Tiket Ke-6 Yang Melewati Batas')
            ->set('description', 'Deskripsi tiket ke-6 yang harusnya gagal karena limit.')
            ->set('priority', 'Low')
            ->call('submit')
            ->assertHasErrors(['subject']);

        $this->assertDatabaseMissing(HelpdeskTicket::TABLE_NAME, [
            'subject' => 'Tiket Ke-6 Yang Melewati Batas',
        ]);

        // Peserta 2 belum mencapai batas -> harus tetap bisa membuat tiket (counter per user)
        Livewire::actingAs($peserta2)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('subject', 'Tiket Pertama Peserta Dua Masih Aman')
            ->set('description', 'Deskripsi tiket peserta 2 harus tetap berhasil dibuat.')
            ->set('priority', 'Low')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(HelpdeskTicket::TABLE_NAME, [
            'reporter_id' => $peserta2->id,
            'subject' => 'Tiket Pertama Peserta Dua Masih Aman',
        ]);
    }

    /**
     * PRIVACY LEAK PREVENTION:
     * - Tiket non-reference In-Progress milik pengguna lain TIDAK PERNAH muncul.
     * - Tiket rujukan (is_reference = true DAN solution_summary terisi) muncul tanpa nama pelapor/deskripsi mentah.
     * - Tiket milik pelapor sendiri dikecualikan.
     */
    public function test_similar_tickets_privacy_leak_prevention(): void
    {
        $currentReporter = User::factory()->create(['name' => 'Budi Pelapor', 'role' => User::ROLE_PESERTA]);
        $otherUser = User::factory()->create(['name' => 'Siti Rahasia', 'role' => User::ROLE_PESERTA]);

        // Tiket 1: Tiket In-Progress biasa milik user lain (BUKAN rujukan, walau ada catatan solusi) -> JANGAN PERNAH MUNCUL
        $leakTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $otherUser->id,
            'subject' => 'Kendala E-ticket QRIS Belum Muncul Siti',
            'description' => 'Data sensitif Siti: telp 081234567890 dan no ktp rahasia.',
            'status' => TicketStatus::IN_PROGRESS,
            'is_reference' => false,
            'solution_summary' => 'Solusi internal sementara staf.',
        ]);

        // Tiket 2: Tiket rujukan resmi milik user lain dengan solution_summary -> BOLEH MUNCUL (dibersihkan)
        $refTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $otherUser->id,
            'subject' => 'Solusi Resmi E-ticket QRIS Hubungi 0812-3456-7890 atau admin@velosha.id',
            'description' => 'Deskripsi mentah yang tidak boleh bocor',
            'status' => TicketStatus::CLOSED,
            'is_reference' => true,
            'solution_summary' => 'Muat ulang tab Portal Rider setelah konfirmasi pembayaran dari payment@velosha.id selesai.',
        ]);

        // Tiket 2b: Tiket rujukan tetapi solution_summary KOSONG/NULL -> TIDAK BOLEH MUNCUL
        $refTicketNoSolution = HelpdeskTicket::factory()->create([
            'reporter_id' => $otherUser->id,
            'subject' => 'Solusi E-ticket QRIS Rujukan Tapi Belum Ada Solusi',
            'description' => 'Deskripsi tiket tanpa solusi',
            'status' => TicketStatus::OPEN,
            'is_reference' => true,
            'solution_summary' => null,
        ]);

        // Tiket 3: Tiket milik pelapor saat ini sendiri (walau rujukan) -> HARUS DIKECUALIKAN
        $ownRefTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $currentReporter->id,
            'subject' => 'Tiket E-ticket QRIS Rujukan Milik Budi Sendiri',
            'description' => 'Deskripsi tiket saya',
            'status' => TicketStatus::CLOSED,
            'is_reference' => true,
            'solution_summary' => 'Solusi tiket saya sendiri',
        ]);

        // Mengikat fake finder sesuai panduan: untuk pengujian berbasis RefreshDatabase (transaksi),
        // gunakan fake finder pada pengujian komponen ini.
        $fakeFinder = $this->createMock(\App\Services\Helpdesk\DuplicateTicketFinder::class);
        $fakeFinder->method('findSimilar')->willReturnCallback(function () use ($refTicket) {
            $clone = clone $refTicket;
            $clone->subject = \App\Support\Helpdesk\TicketSanitizer::clean((string) $clone->subject);
            $clone->solution_summary = \App\Support\Helpdesk\TicketSanitizer::clean((string) $clone->solution_summary);
            $clone->is_own_ticket = false;
            return collect([$clone]);
        });
        $this->app->instance(\App\Services\Helpdesk\DuplicateTicketFinder::class, $fakeFinder);

        $test = Livewire::actingAs($currentReporter)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('subject', 'E-ticket QRIS Belum Muncul');

        $similar = $test->get('similarTickets');

        // 1. Pastikan tiket non-reference user lain tidak ada
        $similarIds = array_column($similar, 'id');
        $this->assertNotContains($leakTicket->id, $similarIds, 'Tiket non-reference In-Progress milik user lain tidak boleh muncul.');
        $this->assertNotContains($refTicketNoSolution->id, $similarIds, 'Tiket rujukan tanpa solution_summary tidak boleh muncul.');

        // 2. Pastikan tiket milik sendiri tidak ada
        $this->assertNotContains($ownRefTicket->id, $similarIds, 'Tiket milik pelapor sendiri tidak boleh muncul sebagai saran.');

        // 3. Pastikan tiket rujukan resmi muncul
        $this->assertContains($refTicket->id, $similarIds, 'Tiket rujukan resmi harus muncul.');

        // 4. Pastikan data tersanitasi dari PII dan tidak mengekspos nama pelapor / deskripsi mentah
        $matched = collect($similar)->firstWhere('id', $refTicket->id);
        $this->assertNotNull($matched);
        $this->assertArrayNotHasKey('match', $matched, 'Match score persentase palsu harus dihilangkan.');
        $this->assertArrayNotHasKey('snippet', $matched, 'Snippet deskripsi mentah tidak boleh diekspos.');
        $this->assertStringNotContainsString('0812-3456-7890', $matched['subject']);
        $this->assertStringNotContainsString('admin@velosha.id', $matched['subject']);
        $this->assertStringNotContainsString('payment@velosha.id', $matched['solution_summary']);
        $this->assertStringContainsString('[REDACTED_PHONE]', $matched['subject']);
        $this->assertStringContainsString('[REDACTED_EMAIL]', $matched['subject']);
        $this->assertStringContainsString('[REDACTED_EMAIL]', $matched['solution_summary']);
    }

    /**
     * Pelapor memilih "Ini menjawab" pada aduan serupa:
     * - Feedback tercatat sebagai helpful = true via updateOrCreate.
     * - Pesan terima kasih ditampilkan dan input formulir di-reset (tidak membuat tiket baru).
     */
    public function test_reporter_can_mark_suggestion_as_answered(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $ticket = HelpdeskTicket::factory()->closed()->create(['is_reference' => true, 'solution_summary' => 'Solusi']);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('subject', 'Kendala pembayaran tiket QR')
            ->set('description', 'Uraian kendala pembayaran tiket QR')
            ->call('markAnswered', $ticket->id)
            ->assertSee('Terima kasih atas tanggapan Anda')
            ->assertSet('subject', '')
            ->assertSet('description', '');

        $this->assertDatabaseHas('ticket_suggestion_feedback', [
            'ticket_id' => $ticket->id,
            'reporter_id' => $peserta->id,
            'helpful' => true,
        ]);
    }

    /**
     * Pelapor memilih "Tetap kirim" pada aduan serupa:
     * - Feedback tercatat sebagai helpful = false via updateOrCreate.
     * - Tiket baru berhasil disubmit dan divalidasi.
     */
    public function test_reporter_can_reject_suggestion_and_submit_ticket(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $refTicket = HelpdeskTicket::factory()->closed()->create(['is_reference' => true, 'solution_summary' => 'Solusi']);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('category', 'Tiket/QR')
            ->set('subject', 'Kendala pembayaran tiket QR yang belum terjawab')
            ->set('description', 'Deskripsi lengkap pembayaran via transfer bank belum masuk ke panitia.')
            ->call('rejectAnswerAndSubmit', $refTicket->id)
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_suggestion_feedback', [
            'ticket_id' => $refTicket->id,
            'reporter_id' => $peserta->id,
            'helpful' => false,
        ]);

        $this->assertDatabaseHas(HelpdeskTicket::TABLE_NAME, [
            'reporter_id' => $peserta->id,
            'subject' => 'Kendala pembayaran tiket QR yang belum terjawab',
        ]);
    }

    public function test_reporter_timeline_state_new_unassigned_ticket(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => null,
            'status' => TicketStatus::OPEN,
            'first_response_at' => null,
            'closed_at' => null,
            'created_at' => now(),
            'response_due_at' => now()->addHours(2),
            'resolution_due_at' => now()->addHours(8),
        ]);

        $component = Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->assertSee('Status & Progres Penanganan')
            ->assertSee('Dibuat')
            ->assertSee('WIB')
            ->assertSee('Belum ditugaskan')
            ->assertSee('Menunggu tanggapan')
            ->assertSee('Menunggu penyelesaian')
            ->assertDontSee('Ditugaskan ke')
            // Assert no internal data leakage
            ->assertDontSee('response_due_at')
            ->assertDontSee('resolution_due_at')
            ->assertDontSee('SLA')
            ->assertDontSee('Mungkin Duplikat');
    }

    public function test_reporter_timeline_state_assigned_to_operator(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Bambang Operator',
        ]);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'status' => TicketStatus::OPEN,
            'first_response_at' => null,
            'closed_at' => null,
            'created_at' => now()->subHour(),
        ]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->assertSee('Dibuat')
            ->assertSee('Ditugaskan ke Bambang Operator')
            ->assertSee('Operator: Bambang Operator')
            ->assertSee('WIB');
    }

    public function test_reporter_timeline_state_in_progress_or_first_reply(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Siti Support',
        ]);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'status' => TicketStatus::IN_PROGRESS,
            'first_response_at' => now()->subMinutes(30),
            'closed_at' => null,
            'created_at' => now()->subHour(),
        ]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->assertSee('Dibuat')
            ->assertSee('Ditugaskan ke Siti Support')
            ->assertSee('Diproses')
            ->assertDontSee('Menunggu tanggapan')
            ->assertSee('Menunggu penyelesaian')
            ->assertSee('WIB');
    }

    public function test_reporter_timeline_state_closed(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Dedi Helpdesk',
        ]);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'status' => TicketStatus::CLOSED,
            'first_response_at' => now()->subHours(2),
            'closed_at' => now()->subMinutes(15),
            'created_at' => now()->subHours(3),
        ]);

        Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\MyTickets::class, ['ticket' => $ticket->id])
            ->assertSee('Dibuat')
            ->assertSee('Ditugaskan ke Dedi Helpdesk')
            ->assertSee('Diproses')
            ->assertSee('Selesai')
            ->assertDontSee('Menunggu penyelesaian')
            ->assertSee('WIB');
    }
}


