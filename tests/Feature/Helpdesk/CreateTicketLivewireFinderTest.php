<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * @file CreateTicketLivewireFinderTest.php
 * @brief Pengujian Livewire CreateTicket terhadap jalur DuplicateTicketFinder riil (FULLTEXT).
 *
 * Menggunakan DatabaseTruncation agar baris data di-COMMIT ke MySQL InnoDB,
 * memungkinkan MATCH ... AGAINST FULLTEXT bekerja persis seperti pada lingkungan produksi.
 */
class CreateTicketLivewireFinderTest extends TestCase
{
    use DatabaseTruncation;

    /**
     * @var array<int, string>
     */
    protected array $tablesToTruncate = [
        'helpdesk_tickets',
        'ticket_responses',
        'ticket_suggestion_feedback',
        'users',
        'reply_templates',
    ];

    protected function afterTruncatingDatabase(): void
    {
        if (\App\Models\SlaPolicy::count() === 0) {
            \Illuminate\Support\Facades\DB::table('sla_policies')->insert([
                ['priority' => 'High', 'response_minutes' => 15, 'resolution_minutes' => 240, 'created_at' => now(), 'updated_at' => now()],
                ['priority' => 'Mid', 'response_minutes' => 60, 'resolution_minutes' => 1440, 'created_at' => now(), 'updated_at' => now()],
                ['priority' => 'Low', 'response_minutes' => 240, 'resolution_minutes' => 4320, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        foreach ($this->tablesToTruncate as $table) {
            \Illuminate\Support\Facades\DB::table($table)->truncate();
        }
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        if (\App\Models\SlaPolicy::count() === 0) {
            \Illuminate\Support\Facades\DB::table('sla_policies')->insert([
                ['priority' => 'High', 'response_minutes' => 15, 'resolution_minutes' => 240, 'created_at' => now(), 'updated_at' => now()],
                ['priority' => 'Mid', 'response_minutes' => 60, 'resolution_minutes' => 1440, 'created_at' => now(), 'updated_at' => now()],
                ['priority' => 'Low', 'response_minutes' => 240, 'resolution_minutes' => 4320, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        parent::tearDown();
    }

    /**
     * Uji jalur pencarian riil FULLTEXT pada komponen Livewire CreateTicket:
     * 1. Tiket milik pelapor sendiri yang berstatus open muncul dengan is_own_ticket = true.
     * 2. Tiket non-reference milik pelapor lain TIDAK PERNAH muncul.
     * 3. Tiket rujukan resmi milik pihak lain muncul dalam kondisi tersanitasi dari PII.
     */
    public function test_livewire_real_fulltext_path_shows_own_ticket_and_sanitizes_reference(): void
    {
        $currentReporter = User::factory()->create([
            'name' => 'Budi Pelapor',
            'email' => 'budi@example.com',
            'role' => User::ROLE_PESERTA,
        ]);

        $otherReporter = User::factory()->create([
            'name' => 'Siti Pelapor Lain',
            'email' => 'siti@example.com',
            'role' => User::ROLE_PESERTA,
        ]);

        // 1. Tiket milik pelapor sendiri (open) dengan subjek serupa
        $ownOpenTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $currentReporter->id,
            'subject' => 'Kendala aktivasi race pack pengambilan bib',
            'description' => 'Pengambilan race pack bib saya di lokasi belum bisa diverifikasi petugas.',
            'status' => TicketStatus::OPEN,
            'is_reference' => false,
        ]);

        // 2. Tiket non-reference milik pelapor lain (in-progress) -> TIDAK BOLEH MUNCUL
        $otherLeakTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $otherReporter->id,
            'subject' => 'Kendala aktivasi race pack bib peserta lain',
            'description' => 'Data sensitif Siti: telp 081234567890 dan no ktp rahasia.',
            'status' => TicketStatus::IN_PROGRESS,
            'is_reference' => false,
            'solution_summary' => 'Solusi internal staf.',
        ]);

        // 3. Tiket rujukan resmi milik pihak lain dengan PII -> HARUS MUNCUL TERSANITASI
        $refTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $otherReporter->id,
            'subject' => 'Solusi aktivasi race pack pengambilan bib hubungi 0812-3456-7890 atau panitia@velosha.id',
            'description' => 'Deskripsi mentah internal yang tidak boleh bocor ke publik.',
            'status' => TicketStatus::CLOSED,
            'is_reference' => true,
            'solution_summary' => 'Bawa kartu identitas ke loket 3 pengambilan race pack atau email support@velosha.id untuk verifikasi.',
        ]);

        // Jalankan pengujian pada komponen Livewire CreateTicket
        $component = Livewire::actingAs($currentReporter)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class)
            ->set('subject', 'Kendala aktivasi race pack pengambilan bib')
            ->set('description', 'Saya ingin aktivasi race pack pengambilan bib saya');

        $similar = $component->get('similarTickets');
        $similarIds = array_column($similar, 'id');

        // 1. Tiket open milik sendiri HARUS muncul dan ditandai is_own_ticket = true
        $this->assertContains(
            $ownOpenTicket->id,
            $similarIds,
            'Tiket terbuka milik pelapor sendiri dengan subjek serupa harus muncul.'
        );
        $ownMatch = collect($similar)->firstWhere('id', $ownOpenTicket->id);
        $this->assertNotNull($ownMatch);
        $this->assertTrue(
            $ownMatch['is_own_ticket'],
            'Tiket milik pelapor sendiri harus memiliki atribut is_own_ticket = true.'
        );

        // 2. Tiket non-reference pihak lain TIDAK PERNAH muncul
        $this->assertNotContains(
            $otherLeakTicket->id,
            $similarIds,
            'Tiket non-reference milik pihak lain tidak boleh muncul.'
        );

        // 3. Tiket rujukan pihak lain muncul dan tersanitasi dari PII
        $this->assertContains(
            $refTicket->id,
            $similarIds,
            'Tiket rujukan resmi pihak lain harus muncul sebagai saran solusi.'
        );
        $refMatch = collect($similar)->firstWhere('id', $refTicket->id);
        $this->assertNotNull($refMatch);
        $this->assertFalse(
            $refMatch['is_own_ticket'],
            'Tiket rujukan pihak lain harus memiliki atribut is_own_ticket = false.'
        );
        $this->assertStringNotContainsString('0812-3456-7890', $refMatch['subject']);
        $this->assertStringNotContainsString('panitia@velosha.id', $refMatch['subject']);
        $this->assertStringContainsString('[REDACTED_PHONE]', $refMatch['subject']);
        $this->assertStringContainsString('[REDACTED_EMAIL]', $refMatch['subject']);
        $this->assertStringNotContainsString('support@velosha.id', $refMatch['solution_summary']);
        $this->assertStringContainsString('[REDACTED_EMAIL]', $refMatch['solution_summary']);
    }

    /**
     * Uji alur pemungutan suara saran solusi (vote path):
     * Memastikan pelapor dapat memberikan suara dan memperbarui suaranya via updateOrCreate,
     * tanpa melanggar UNIQUE constraint (ticket_id, reporter_id) di database.
     */
    public function test_reporter_can_vote_and_update_vote_on_suggestion(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $ticket = HelpdeskTicket::factory()->closed()->create(['is_reference' => true, 'solution_summary' => 'Solusi']);

        $component = Livewire::actingAs($peserta)
            ->test(\App\Livewire\Helpdesk\CreateTicket::class);

        // Suara pertama: markAnswered
        $component->call('markAnswered', $ticket->id);
        $this->assertDatabaseHas('ticket_suggestion_feedback', [
            'ticket_id' => $ticket->id,
            'reporter_id' => $peserta->id,
            'helpful' => true,
        ]);

        // Suara kedua pada tiket yang sama: harus memperbarui record via updateOrCreate tanpa memicu duplicate entry exception
        $component->call('markAnswered', $ticket->id);
        $this->assertSame(
            1,
            \App\Models\TicketSuggestionFeedback::where('ticket_id', $ticket->id)
                ->where('reporter_id', $peserta->id)
                ->count()
        );
    }
}
