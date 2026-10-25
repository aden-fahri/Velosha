<?php

namespace Tests\Unit\Helpdesk;

use App\Enums\Helpdesk\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\ReplyTemplate;
use App\Models\TicketResponse;
use App\Models\User;
use App\Services\Helpdesk\AutoReplyService;
use App\Support\Helpdesk\SystemUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AutoReplyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AutoReplyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AutoReplyService();
    }

    /**
     * Provider data untuk berbagai format teks kosong / whitespace / placeholder kosong.
     *
     * @return array<string, array{0: string}>
     */
    public static function blankTemplateProvider(): array
    {
        return [
            'empty string' => [''],
            'single space' => [' '],
            'multiple spaces' => ['     '],
            'newline and tab' => ["\n\t"],
            'mixed whitespace' => ["  \r\n\t  "],
            'unknown placeholder only' => ['{{unknown_placeholder}}'],
            'unknown placeholder with whitespace' => ["  {{unknown_foo}} \n\t "],
            'multiple unknown placeholders' => ['{{foo}} {{bar}} {{baz}}'],
        ];
    }

    #[DataProvider('blankTemplateProvider')]
    public function test_build_returns_null_for_blank_or_whitespace_templates(string $rawTemplate): void
    {
        $reporter = User::factory()->create(['name' => 'Budi Peserta']);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'category' => TicketCategory::TIKET_QR,
        ]);

        $result = $this->service->build($rawTemplate, $ticket);

        $this->assertNull($result, "Template '{$rawTemplate}' harus mengembalikan null.");
    }

    public function test_build_replaces_placeholders_and_strips_unknown_placeholders(): void
    {
        $reporter = User::factory()->create(['name' => 'Siti Peserta']);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'category' => TicketCategory::TIKET_QR,
        ]);

        $rawTemplate = "Halo {{reporter_name}}, kendala tiket {{ticket_id}} berkategori {{ticket_category}} sedang diproses. {{unknown_field}} Terima kasih!";

        $result = $this->service->build($rawTemplate, $ticket);

        $this->assertNotNull($result);
        $this->assertStringContainsString('Halo Siti Peserta', $result);
        $this->assertStringContainsString('tiket #' . $ticket->id, $result);
        $this->assertStringContainsString('berkategori Tiket/QR', $result);
        $this->assertStringNotContainsString('{{unknown_field}}', $result);
        $this->assertStringNotContainsString('{{', $result);
    }

    public function test_send_with_blank_template_creates_zero_responses(): void
    {
        $reporter = User::factory()->create(['name' => 'Andi']);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'category' => TicketCategory::PEMBAYARAN,
        ]);

        $response = $this->service->send($ticket, "   \n\t  {{unknown_key}}  ");

        $this->assertNull($response);
        $this->assertSame(0, TicketResponse::where('ticket_id', $ticket->id)->count());
    }

    public function test_send_successful_auto_reply_creates_response_with_bot_author(): void
    {
        $reporter = User::factory()->create(['name' => 'Rina']);
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'category' => TicketCategory::TIKET_QR,
            'status' => TicketStatus::OPEN,
            'first_response_at' => null,
            'operator_id' => null,
        ]);

        $template = ReplyTemplate::create([
            'title' => 'Auto-reply Tiket QR Test',
            'category' => TicketCategory::TIKET_QR,
            'body' => 'Halo {{reporter_name}}, tiket {{ticket_id}} untuk {{ticket_category}} telah tercatat.',
            'is_active' => true,
        ]);

        $response = $this->service->send($ticket, $template);

        $this->assertNotNull($response);
        $this->assertSame($ticket->id, $response->ticket_id);
        $this->assertTrue($response->is_auto_reply);
        $this->assertStringContainsString('Halo Rina, tiket #' . $ticket->id . ' untuk Tiket/QR telah tercatat.', $response->message);

        // Pastikan responder adalah akun bot sistem
        $bot = SystemUser::get();
        $this->assertSame($bot->id, $response->responder_id);
        $this->assertTrue($bot->is_system);

        // Verifikasi invariant tiket
        $ticket->refresh();
        $this->assertSame(TicketStatus::OPEN, $ticket->status);
        $this->assertNull($ticket->first_response_at);
        $this->assertNull($ticket->operator_id);
    }

    public function test_suggestions_scoring_by_category_and_keywords_with_deterministic_tie_break(): void
    {
        // Bersihkan template bawaan seeder agar uji skor murni
        ReplyTemplate::query()->delete();

        // Template 1: Kategori sama (Tiket/QR: 10 pt) + 2 kata kunci cocok (bib, qr: 4 pt) = 14 pt
        $t1 = ReplyTemplate::create([
            'title' => 'Solusi QR dan BIB',
            'category' => TicketCategory::TIKET_QR,
            'body' => 'Solusi nomor BIB dan kode QR.',
            'keyword_triggers' => ['bib', 'qr', 'barcode'],
            'use_count' => 5,
            'is_active' => true,
        ]);

        // Template 2: Kategori sama (Tiket/QR: 10 pt) + 0 kata kunci cocok = 10 pt
        $t2 = ReplyTemplate::create([
            'title' => 'Solusi Umum Tiket',
            'category' => TicketCategory::TIKET_QR,
            'body' => 'Informasi umum tiket.',
            'keyword_triggers' => ['jadwal', 'waktu'],
            'use_count' => 20,
            'is_active' => true,
        ]);

        // Template 3: Kategori beda (Pembayaran: 0 pt) + 1 kata kunci cocok (bib: 2 pt) = 2 pt
        $t3 = ReplyTemplate::create([
            'title' => 'Pembayaran Terkait BIB',
            'category' => TicketCategory::PEMBAYARAN,
            'body' => 'Informasi biaya BIB tambahan.',
            'keyword_triggers' => ['bib', 'va'],
            'use_count' => 10,
            'is_active' => true,
        ]);

        // Template 4: Kategori beda (Lokasi: 0 pt) + 0 kata kunci = 0 pt
        $t4 = ReplyTemplate::create([
            'title' => 'Panduan Lokasi Venue',
            'category' => TicketCategory::LOKASI,
            'body' => 'Panduan rute dan tempat expo.',
            'keyword_triggers' => ['rute', 'expo'],
            'use_count' => 0,
            'is_active' => true,
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'category' => TicketCategory::TIKET_QR,
            'subject' => 'Kendala nomor BIB dan scan QR',
            'description' => 'Saya belum menerima cetak nomor bib dan qr code saya tidak terbaca.',
        ]);

        $suggestions = $this->service->getSuggestions($ticket, 3);

        $this->assertCount(3, $suggestions);
        $this->assertSame($t1->id, $suggestions[0]->id, 'Template 1 dengan skor 14 harus menjadi rekomendasi pertama');
        $this->assertSame($t2->id, $suggestions[1]->id, 'Template 2 dengan skor 10 harus menjadi rekomendasi kedua');
        $this->assertSame($t3->id, $suggestions[2]->id, 'Template 3 dengan skor 2 harus menjadi rekomendasi ketiga');
        $this->assertFalse($suggestions->contains('id', $t4->id), 'Template 4 berskor 0 tidak boleh masuk 3 besar');
    }

    public function test_suggestions_privacy_never_exposes_other_reporters_data(): void
    {
        // Buat tiket milik pengguna lain yang berisi data sensitif
        $otherUser = User::factory()->create([
            'name' => 'Budi Rahasia',
            'email' => 'rahasia@budi.com',
            'phone' => '081299998888',
        ]);

        $secretTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $otherUser->id,
            'category' => TicketCategory::TIKET_QR,
            'subject' => 'Data sensitif NIK 3201019900001 dan no 081299998888',
            'description' => 'Tolong rahasiakan data pribadi rahasia@budi.com',
        ]);

        // Tiket yang sedang dibuka operator
        $currentTicket = HelpdeskTicket::factory()->create([
            'category' => TicketCategory::TIKET_QR,
            'subject' => 'Pertanyaan tiket QR saya',
            'description' => 'Bagaimana cara unduh e-ticket saya?',
        ]);

        ReplyTemplate::create([
            'title' => 'Template Bantuan Umum',
            'category' => TicketCategory::TIKET_QR,
            'body' => 'Gunakan fitur unduh e-ticket pada dashboard portal rider.',
            'keyword_triggers' => ['tiket', 'qr'],
            'is_active' => true,
        ]);

        $suggestions = $this->service->getSuggestions($currentTicket, 3);

        $this->assertNotEmpty($suggestions);
        foreach ($suggestions as $suggestion) {
            $this->assertInstanceOf(ReplyTemplate::class, $suggestion);
            $this->assertStringNotContainsString('Budi Rahasia', $suggestion->body);
            $this->assertStringNotContainsString('rahasia@budi.com', $suggestion->body);
            $this->assertStringNotContainsString('081299998888', $suggestion->body);
            $this->assertStringNotContainsString('3201019900001', $suggestion->body);
        }
    }

    public function test_record_template_usage_increments_use_count_atomically(): void
    {
        $template = ReplyTemplate::create([
            'title' => 'Template Uji Use Count',
            'category' => TicketCategory::LAINNYA,
            'body' => 'Isi template.',
            'use_count' => 3,
            'is_active' => true,
        ]);

        $this->service->recordTemplateUsage($template->id);

        $template->refresh();
        $this->assertSame(4, $template->use_count);

        $this->service->recordTemplateUsage($template);

        $template->refresh();
        $this->assertSame(5, $template->use_count);
    }
}
