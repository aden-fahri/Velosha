<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\TicketSuggestionFeedback;
use App\Models\User;
use App\Services\Helpdesk\DuplicateTicketFinder;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\TestCase;

/**
 * @file DuplicateTicketFinderTest.php
 * @brief Pengujian DuplicateTicketFinder menggunakan DatabaseTruncation.
 *
 * Catatan Penting:
 * InnoDB FULLTEXT MySQL hanya dapat mengindeks dan mencari baris data yang sudah di-COMMIT ke disk.
 * Oleh karena itu, pengujian ini secara ketat menggunakan DatabaseTruncation (bukan RefreshDatabase/transaksi).
 */
class DuplicateTicketFinderTest extends TestCase
{
    use DatabaseTruncation;

    /**
     * Batasi tabel yang di-truncate hanya pada tabel pengujian helpdesk & users,
     * agar tabel benih sistem seperti sla_policies tidak terhapus dan suite tetap bersih.
     *
     * @var array<int, string>
     */
    protected array $tablesToTruncate = [
        'helpdesk_tickets',
        'ticket_responses',
        'ticket_suggestion_feedback',
        'users',
        'reply_templates',
    ];

    private DuplicateTicketFinder $finder;

    protected function afterTruncatingDatabase(): void
    {
        // Pastikan default sla_policies selalu tersedia untuk pengujian selanjutnya
        if (\App\Models\SlaPolicy::count() === 0) {
            \Illuminate\Support\Facades\DB::table('sla_policies')->insert([
                ['priority' => 'High', 'response_minutes' => 15, 'resolution_minutes' => 240, 'created_at' => now(), 'updated_at' => now()],
                ['priority' => 'Mid', 'response_minutes' => 60, 'resolution_minutes' => 1440, 'created_at' => now(), 'updated_at' => now()],
                ['priority' => 'Low', 'response_minutes' => 240, 'resolution_minutes' => 4320, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->finder = app(DuplicateTicketFinder::class);
    }

    /**
     * Uji rumus pembobotan ranking:
     * final = fulltext_score * (1 + ln(1 + helpful)) / (1 + ln(1 + not_helpful))
     * Formula harus selalu bernilai positif dan diuji dengan angka eksak.
     */
    public function test_exact_formula_calculation_and_positive_score(): void
    {
        // 1. Tanpa feedback: helpful = 0, not_helpful = 0 => multiplier = (1 + ln(1)) / (1 + ln(1)) = 1.0
        $scoreNoFeedback = $this->finder->calculateFinalScore(2.5, 0, 0);
        $this->assertEqualsWithDelta(2.5, $scoreNoFeedback, 0.00001);

        // 2. Feedback positif: helpful = 2, not_helpful = 0
        // multiplier = (1 + ln(3)) / (1 + ln(1)) = (1 + 1.098612288668) / 1.0 = 2.098612288668
        // final = 2.0 * 2.098612288668 = 4.197224577336
        $scorePositive = $this->finder->calculateFinalScore(2.0, 2, 0);
        $expectedPositive = 2.0 * (1.0 + log(3.0));
        $this->assertEqualsWithDelta($expectedPositive, $scorePositive, 0.00001);
        $this->assertGreaterThan(0.0, $scorePositive);

        // 3. Feedback negatif: helpful = 0, not_helpful = 1
        // multiplier = (1 + ln(1)) / (1 + ln(2)) = 1.0 / (1 + 0.69314718056) = 0.590616129138
        // final = 2.0 * 0.590616129138 = 1.181232258276
        $scoreNegative = $this->finder->calculateFinalScore(2.0, 0, 1);
        $expectedNegative = 2.0 / (1.0 + log(2.0));
        $this->assertEqualsWithDelta($expectedNegative, $scoreNegative, 0.00001);
        $this->assertGreaterThan(0.0, $scoreNegative);

        // 4. Feedback campuran: helpful = 3, not_helpful = 1
        // multiplier = (1 + ln(4)) / (1 + ln(2))
        $scoreMixed = $this->finder->calculateFinalScore(2.0, 3, 1);
        $expectedMixed = 2.0 * (1.0 + log(4.0)) / (1.0 + log(2.0));
        $this->assertEqualsWithDelta($expectedMixed, $scoreMixed, 0.00001);
        $this->assertGreaterThan(0.0, $scoreMixed);

        // 5. Penanganan nilai negatif (defensif): harus tetap positif
        $scoreDefensive = $this->finder->calculateFinalScore(1.5, -5, -2);
        $this->assertEqualsWithDelta(1.5, $scoreDefensive, 0.00001);
    }

    /**
     * Tiket milik pelapor sendiri (semua status: Open, In-Progress, Closed)
     * WAJIB dikembalikan dan ditampilkan secara utuh sebagai kandidat duplikat utama.
     */
    public function test_reporter_own_ticket_found_across_any_status(): void
    {
        $reporter = User::factory()->create();

        // Buat 3 tiket milik pelapor sendiri: Open, In-Progress, Closed
        $openTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'status' => TicketStatus::OPEN,
            'subject' => 'Pembayaran e-ticket QR Code belum terbit',
            'description' => 'Saya sudah mentransfer untuk registrasi balap namun QR Code e-ticket belum keluar di portal.',
        ]);

        $inProgressTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'status' => TicketStatus::IN_PROGRESS,
            'subject' => 'Kendala pembayaran QR Code registrasi',
            'description' => 'Pembayaran registrasi via transfer QR Code masih pending.',
        ]);

        $closedTicket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $reporter->id,
            'subject' => 'Masalah pembayaran e-ticket QR Code lunas',
            'description' => 'QR Code e-ticket tidak muncul pada tiket saya di portal.',
        ]);

        $results = $this->finder->findSimilar(
            $reporter,
            'Pembayaran e-ticket QR Code belum keluar',
            'QR Code tidak muncul setelah bayar',
            3,
            0.001
        );

        $this->assertNotEmpty($results);
        $this->assertContains($openTicket->id, $results->pluck('id'));
        $this->assertContains($inProgressTicket->id, $results->pluck('id'));
        $this->assertContains($closedTicket->id, $results->pluck('id'));

        // Pastikan tiket milik sendiri ditandai is_own_ticket = true dan deskripsinya tidak dikosongkan
        $firstMatch = $results->first();
        $this->assertTrue($firstMatch->is_own_ticket);
        $this->assertNotEmpty($firstMatch->description);
    }

    /**
     * Tiket pelapor lain HANYA muncul jika is_reference = true DAN solution_summary tidak kosong.
     * Data yang dikembalikan disanitasi ketat (hanya subject dan solution_summary bersih, tanpa deskripsi/relasi pelapor).
     */
    public function test_other_reporters_reference_ticket_found_with_sanitized_data_only(): void
    {
        $currentReporter = User::factory()->create();
        $otherReporter = User::factory()->create([
            'name' => 'Budi Rahasia',
            'email' => 'budi.rahasia@example.com',
        ]);

        $refTicket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $otherReporter->id,
            'is_reference' => true,
            'subject' => 'Panduan verifikasi e-ticket QR Code dan nomor BIB balap',
            'description' => 'Deskripsi sensitif pengguna lain dengan data pribadi seperti KTP dan nomor telepon.',
            'solution_summary' => 'Silakan refresh portal rider pada menu Tiket & BIB untuk mengunduh QR Code resmi.',
        ]);

        $results = $this->finder->findSimilar(
            $currentReporter,
            'Verifikasi e-ticket QR Code dan nomor BIB balap',
            'Cara cek e-ticket dan nomor BIB',
            3,
            0.001
        );

        $this->assertNotEmpty($results);
        $found = $results->firstWhere('id', $refTicket->id);
        $this->assertNotNull($found);

        // Aturan privasi:
        $this->assertFalse($found->is_own_ticket);
        $this->assertSame('', $found->description, 'Deskripsi tiket pelapor lain harus dikosongkan.');
        $this->assertFalse($found->relationLoaded('reporter'), 'Relasi reporter tidak boleh dimuat untuk menjaga privasi.');
        $this->assertStringContainsString('Silakan refresh portal rider', $found->solution_summary);
    }

    /**
     * Tiket biasa milik orang lain (bukan rujukan atau tanpa ringkasan solusi)
     * TIDAK PERNAH muncul dalam hasil pencarian aduan serupa.
     */
    public function test_other_reporters_non_reference_ticket_never_found(): void
    {
        $currentReporter = User::factory()->create();
        $otherReporter = User::factory()->create();

        // 1. Tiket biasa orang lain yang masih Open
        $otherOpen = HelpdeskTicket::factory()->create([
            'reporter_id' => $otherReporter->id,
            'is_reference' => false,
            'subject' => 'Kendala jadwal balap dan nomor BIB velosha',
            'description' => 'Jadwal balap dan nomor BIB saya belum masuk di kategori race.',
        ]);

        // 2. Tiket tertutup orang lain tetapi is_reference = false
        $otherClosedNoRef = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $otherReporter->id,
            'is_reference' => false,
            'subject' => 'Pertanyaan jadwal balap dan nomor BIB velosha',
            'description' => 'Jadwal balap dan nomor BIB saya.',
            'solution_summary' => 'Sudah diarahkan ke panitia.',
        ]);

        // 3. Tiket is_reference = true tetapi solution_summary kosong
        $otherRefEmptySolution = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $otherReporter->id,
            'is_reference' => true,
            'subject' => 'Info jadwal balap dan nomor BIB velosha',
            'description' => 'Jadwal balap dan nomor BIB.',
            'solution_summary' => '',
        ]);

        $results = $this->finder->findSimilar(
            $currentReporter,
            'Jadwal balap dan nomor BIB velosha',
            'Cek jadwal balap dan nomor BIB',
            5,
            0.001
        );

        $this->assertFalse($results->contains('id', $otherOpen->id));
        $this->assertFalse($results->contains('id', $otherClosedNoRef->id));
        $this->assertFalse($results->contains('id', $otherRefEmptySolution->id));
    }

    /**
     * Uji pemeringkatan dengan feedback nyata: feedback positif menaikkan peringkat,
     * feedback negatif menurunkan peringkat sesuai formula logaritmik.
     */
    public function test_feedback_changes_ranking_via_exact_formula(): void
    {
        $currentReporter = User::factory()->create();
        $otherReporter = User::factory()->create();

        // Buat 2 tiket rujukan dengan teks identik
        $ticketA = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $otherReporter->id,
            'is_reference' => true,
            'subject' => 'Kendala teknis pembayaran QR Code aplikasi mobile Velosha',
            'description' => 'Penjelasan kendala teknis transaksi QR Code.',
            'solution_summary' => 'Solusi A: Muat ulang aplikasi dan bersihkan cache.',
        ]);

        $ticketB = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $otherReporter->id,
            'is_reference' => true,
            'subject' => 'Kendala teknis pembayaran QR Code aplikasi mobile Velosha',
            'description' => 'Penjelasan kendala teknis transaksi QR Code.',
            'solution_summary' => 'Solusi B: Cek mutasi rekening bank Anda.',
        ]);

        // Tiket A mendapat 3 umpan balik positif
        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->create();
            TicketSuggestionFeedback::create([
                'ticket_id' => $ticketA->id,
                'reporter_id' => $user->id,
                'helpful' => true,
                'created_at' => now(),
            ]);
        }

        // Tiket B mendapat 2 umpan balik negatif
        for ($i = 0; $i < 2; $i++) {
            $user = User::factory()->create();
            TicketSuggestionFeedback::create([
                'ticket_id' => $ticketB->id,
                'reporter_id' => $user->id,
                'helpful' => false,
                'created_at' => now(),
            ]);
        }

        $results = $this->finder->findSimilar(
            $currentReporter,
            'Kendala teknis pembayaran QR Code aplikasi mobile Velosha',
            'Transaksi QR Code aplikasi mobile Velosha',
            2,
            0.001
        );

        $this->assertCount(2, $results);
        $this->assertSame($ticketA->id, $results[0]->id, 'Tiket A dengan umpan balik positif harus berada di peringkat 1.');
        $this->assertSame($ticketB->id, $results[1]->id, 'Tiket B dengan umpan balik negatif harus berada di peringkat 2.');
        $this->assertGreaterThan($results[1]->final_score, $results[0]->final_score);
    }

    /**
     * Uji threshold menyaring kecocokan yang tidak relevan / lemah.
     */
    public function test_threshold_filters_out_weak_matches(): void
    {
        $reporter = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'subject' => 'Lokasi venue balap sepeda Bandung',
            'description' => 'Alamat lengkap lokasi sirkuit dan titik kumpul.',
        ]);

        // Query yang sama sekali berbeda topiknya
        $results = $this->finder->findSimilar(
            $reporter,
            'Pengembalian dana refund tiket batal',
            'Prosedur refund pembayaran transfer bank',
            3,
            0.5 // Threshold tinggi
        );

        $this->assertEmpty($results);
    }

    /**
     * Stopword list & short tokens:
     * Query yang hanya berisi kata sambung/stopword atau token kurang dari 2 karakter
     * TIDAK BOLEH memicu error SQL dan harus mengembalikan koleksi kosong secara aman.
     */
    public function test_query_with_only_stopwords_or_short_tokens_returns_empty_collection_without_sql_error(): void
    {
        $reporter = User::factory()->create();

        // Seluruh token adalah stopword atau token 1 karakter
        $results = $this->finder->findSimilar(
            $reporter,
            'yang untuk pada ke a b',
            'dan ini karena oleh saat di',
            3
        );

        $this->assertTrue($results->isEmpty());
    }

    /**
     * Istilah pendek khas Indonesia seperti "qr", "gps", "bib" harus dipertahankan
     * oleh normalizeQuery() dan tidak membuat query menjadi kosong.
     */
    public function test_short_tokens_like_qr_gps_bib_are_preserved_in_normalized_query(): void
    {
        $query = 'kendala tiket qr gps bib velosha';
        $normalized = $this->finder->normalizeQuery($query);

        $this->assertStringContainsString('qr', $normalized);
        $this->assertStringContainsString('gps', $normalized);
        $this->assertStringContainsString('bib', $normalized);
        $this->assertStringContainsString('velosha', $normalized);
    }

    /**
     * Uji pencarian duplikat oleh operator menggunakan findDuplicatesForTicket():
     * Hanya mencari tiket aktif (bukan Closed), bukan tiket dirinya sendiri,
     * dan mengabaikan tiket yang sudah ditutup.
     */
    public function test_operator_find_duplicates_finds_active_tickets_across_reporters(): void
    {
        $reporterA = User::factory()->create();
        $reporterB = User::factory()->create();

        $targetTicket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporterA->id,
            'status' => TicketStatus::OPEN,
            'subject' => 'Kesalahan data tanggal lahir peserta race Velosha',
            'description' => 'Tanggal lahir di profil balap saya keliru dan perlu diperbaiki.',
        ]);

        $duplicateCandidate = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporterB->id,
            'status' => TicketStatus::OPEN,
            'subject' => 'Kesalahan data tanggal lahir peserta race Velosha',
            'description' => 'Tanggal lahir di profil balap saya keliru dan perlu diperbaiki panitia.',
        ]);

        $closedTicket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $reporterB->id,
            'subject' => 'Kesalahan data tanggal lahir peserta race Velosha',
            'description' => 'Tanggal lahir di profil balap saya keliru.',
        ]);

        $duplicates = $this->finder->findDuplicatesForTicket($targetTicket, 3, 0.001);

        $this->assertTrue($duplicates->contains('id', $duplicateCandidate->id));
        $this->assertFalse($duplicates->contains('id', $targetTicket->id), 'Tiket target tidak boleh muncul sebagai duplikat dirinya sendiri.');
        $this->assertFalse($duplicates->contains('id', $closedTicket->id), 'Tiket yang sudah ditutup tidak boleh muncul sebagai kandidat duplikat.');
    }
}
