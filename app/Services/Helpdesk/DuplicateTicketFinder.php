<?php

namespace App\Services\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Support\Helpdesk\TicketSanitizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @file DuplicateTicketFinder.php
 * @brief Layanan pendeteksi tiket duplikat dan aduan serupa menggunakan MySQL FULLTEXT.
 *
 * @details
 * Layanan ini mencari tiket yang mirip berdasarkan pencarian teks lengkap MySQL (MATCH ... AGAINST
 * IN NATURAL LANGUAGE MODE) pada kolom subject dan description.
 *
 * Aturan Privasi & Cakupan Pencarian:
 * 1. Tiket milik pelapor sendiri (semua status: Open, In-Progress, Closed) ditampilkan secara utuh.
 * 2. Tiket pelapor lain HANYA ditampilkan jika is_reference = true DAN solution_summary tidak kosong.
 * 3. Tiket rujukan pelapor lain disanitasi menggunakan TicketSanitizer (hanya subject & solution_summary
 *    bersih yang ditampilkan; deskripsi asli dan data pelapor tidak dimuat).
 * 4. Tiket biasa milik pelapor lain tidak pernah muncul.
 *
 * Keputusan N-Gram Parser:
 * MySQL InnoDB FULLTEXT bawaan menggunakan parser berbasis kata (whitespace/punctuation delimited)
 * dengan innodb_ft_min_token_size (default 3). Bahasa Indonesia adalah bahasa beraksara Latin dengan
 * pemisah spasi antar kata, sehingga parser standar sangat efektif ketika stopword disaring dan
 * token di-normalisasi. Penggunaan ngram parser dihindari karena meningkatkan ukuran indeks secara
 * eksponensial (bigram) dan menyebabkan false positive pada kemunculan substring kata umum.
 *
 * @package App\Services\Helpdesk
 * @author Velosha Core Engineering
 * @version 1.0.0
 */
class DuplicateTicketFinder
{
    /**
     * Ambang batas relevansi minimum default untuk MySQL FULLTEXT.
     */
    public const DEFAULT_THRESHOLD = 0.01;

    /**
     * Jumlah rekomendasi maksimal default.
     */
    public const DEFAULT_LIMIT = 3;

    /**
     * Daftar stopword bahasa Indonesia untuk normalisasi query pencarian.
     * Kata-kata fungsional ini disaring agar query FULLTEXT berfokus pada istilah inti.
     *
     * @var array<int, string>
     */
    public const INDONESIAN_STOPWORDS = [
        'yang', 'untuk', 'pada', 'ke', 'para', 'namun', 'menurut', 'antara', 'dia',
        'dua', 'ia', 'seperti', 'jika', 'sehingga', 'kembali', 'dan', 'ini', 'karena',
        'oleh', 'saat', 'dari', 'di', 'telah', 'dalam', 'bisa', 'ada', 'tidak', 'dengan',
        'saya', 'kami', 'kita', 'kamu', 'anda', 'mereka', 'akan', 'atau', 'sudah',
        'juga', 'hanya', 'agar', 'bagi', 'bahwa', 'serta', 'tentang', 'setelah',
        'lagi', 'pun', 'adalah', 'yaitu', 'yakni', 'apakah', 'bagaimana', 'kenapa',
    ];

    /**
     * Membersihkan dan menormalisasi teks query pencarian.
     *
     * Mengubah ke huruf kecil, menghapus karakter selain alfanumerik, menyaring stopword,
     * serta menangani token pendek agar tidak menghasilkan query rusak.
     *
     * @param string $text Teks input (gabungan subjek dan deskripsi)
     * @return string Query yang telah dinormalisasi atau string kosong jika hanya stopword
     */
    public function normalizeQuery(string $text): string
    {
        $clean = mb_strtolower(trim($text));
        $clean = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $clean);

        if ($clean === null || trim($clean) === '') {
            return '';
        }

        $tokens = preg_split('/\s+/u', trim($clean), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($tokens)) {
            return '';
        }

        $filtered = array_filter($tokens, function (string $token) {
            return ! in_array($token, self::INDONESIAN_STOPWORDS, true) && mb_strlen($token) >= 2;
        });

        return implode(' ', $filtered);
    }

    /**
     * Menghitung skor akhir rekomendasi dengan pembobotan feedback.
     *
     * Rumus: final = fulltext_score * (1 + ln(1 + helpful)) / (1 + ln(1 + not_helpful))
     * Formula ini selalu menghasilkan nilai positif dan mengapresiasi solusi yang terbukti membantu
     * serta menurunkan peringkat rekomendasi yang sering ditolak pelapor.
     *
     * @param float $fulltextScore Skor relevansi dari MySQL FULLTEXT
     * @param int $helpful Jumlah umpan balik positif
     * @param int $notHelpful Jumlah umpan balik negatif
     * @return float Skor terbobot
     */
    public function calculateFinalScore(float $fulltextScore, int $helpful, int $notHelpful): float
    {
        $helpfulCount = max(0, $helpful);
        $notHelpfulCount = max(0, $notHelpful);

        $multiplier = (1.0 + log(1.0 + $helpfulCount)) / (1.0 + log(1.0 + $notHelpfulCount));

        return $fulltextScore * $multiplier;
    }

    /**
     * Mencari tiket serupa atau duplikat potensial.
     *
     * @param User $reporter Pengguna yang sedang mencari atau membuat tiket
     * @param string $subject Subjek tiket yang diketikkan
     * @param string $description Deskripsi kendala tiket
     * @param int $limit Batas maksimal hasil pencarian (default 3)
     * @param float $threshold Batas minimum skor relevansi FULLTEXT (default 0.01)
     * @param int|null $excludeTicketId ID tiket yang dikecualikan (misal tiket saat ini)
     * @return Collection<int, HelpdeskTicket> Koleksi tiket serupa yang terurut
     */
    public function findSimilar(
        User $reporter,
        string $subject,
        string $description,
        int $limit = self::DEFAULT_LIMIT,
        float $threshold = self::DEFAULT_THRESHOLD,
        ?int $excludeTicketId = null
    ): Collection {
        $rawQuery = trim($subject . ' ' . $description);
        $normalized = $this->normalizeQuery($rawQuery);

        // Jika setelah normalisasi dan stopword filtering tidak tersisa kata pencarian,
        // kembalikan koleksi kosong secara aman tanpa memicu query SQL.
        if ($normalized === '') {
            return collect();
        }

        // Subquery agregasi feedback untuk performa optimal tanpa GROUP BY penuh di query utama
        $feedbackSubquery = DB::table('ticket_suggestion_feedback')
            ->select('ticket_id')
            ->selectRaw('SUM(CASE WHEN helpful = 1 THEN 1 ELSE 0 END) AS helpful_count')
            ->selectRaw('SUM(CASE WHEN helpful = 0 THEN 1 ELSE 0 END) AS not_helpful_count')
            ->groupBy('ticket_id');

        $reporterId = $reporter->id;

        $results = HelpdeskTicket::query()
            ->select('helpdesk_tickets.*')
            ->selectRaw('COALESCE(fb.helpful_count, 0) AS helpful_count')
            ->selectRaw('COALESCE(fb.not_helpful_count, 0) AS not_helpful_count')
            ->selectRaw('MATCH(helpdesk_tickets.subject, helpdesk_tickets.description) AGAINST (? IN NATURAL LANGUAGE MODE) AS fulltext_score', [$normalized])
            ->leftJoinSub($feedbackSubquery, 'fb', 'fb.ticket_id', '=', 'helpdesk_tickets.id')
            ->where(function ($query) use ($reporterId) {
                // 1. Tiket milik pelapor sendiri (semua status: Open, In Progress, Closed)
                $query->where('helpdesk_tickets.reporter_id', $reporterId)
                    // 2. ATAU tiket rujukan orang lain yang memiliki solusi resmi
                    ->orWhere(function ($q) use ($reporterId) {
                        $q->where('helpdesk_tickets.reporter_id', '!=', $reporterId)
                            ->where('helpdesk_tickets.is_reference', true)
                            ->whereNotNull('helpdesk_tickets.solution_summary')
                            ->where('helpdesk_tickets.solution_summary', '!=', '');
                    });
            })
            ->when($excludeTicketId !== null, fn ($q) => $q->where('helpdesk_tickets.id', '!=', $excludeTicketId))
            ->having('fulltext_score', '>=', $threshold)
            ->get();

        if ($results->isEmpty()) {
            return collect();
        }

        // Hitung skor akhir dengan formula pembobotan feedback dan urutkan
        $scored = $results->map(function (HelpdeskTicket $ticket) use ($reporterId) {
            $ftScore = (float) $ticket->fulltext_score;
            $helpful = (int) $ticket->helpful_count;
            $notHelpful = (int) $ticket->not_helpful_count;

            $ticket->final_score = $this->calculateFinalScore($ftScore, $helpful, $notHelpful);

            if ($ticket->reporter_id === $reporterId) {
                $ticket->is_own_ticket = true;
            } else {
                // Terapkan sanitasi privasi ketat untuk tiket rujukan pihak lain
                $ticket->is_own_ticket = false;
                $ticket->subject = TicketSanitizer::clean((string) $ticket->subject);
                $ticket->solution_summary = TicketSanitizer::clean((string) $ticket->solution_summary);
                $ticket->description = '';
                $ticket->unsetRelation('reporter');
            }

            return $ticket;
        });

        // Urutkan secara deterministik: final_score DESC, fulltext_score DESC, id ASC
        return $scored->sort(function (HelpdeskTicket $a, HelpdeskTicket $b) {
            if ($a->final_score != $b->final_score) {
                return $b->final_score <=> $a->final_score;
            }

            if ($a->fulltext_score != $b->fulltext_score) {
                return $b->fulltext_score <=> $a->fulltext_score;
            }

            return $a->id <=> $b->id;
        })->take($limit)->values();
    }

    /**
     * Mencari tiket aktif lain yang berpotensi menjadi duplikat dari tiket sasaran untuk operator.
     *
     * @param HelpdeskTicket $ticket Tiket sasaran
     * @param int $limit Batas maksimal hasil pencarian (default 3)
     * @param float $threshold Batas minimum relevansi FULLTEXT (default 0.01)
     * @return Collection<int, HelpdeskTicket>
     */
    public function findDuplicatesForTicket(
        HelpdeskTicket $ticket,
        int $limit = self::DEFAULT_LIMIT,
        float $threshold = self::DEFAULT_THRESHOLD
    ): Collection {
        if ($ticket->isClosed() || $ticket->duplicate_of_id !== null) {
            return collect();
        }

        $rawQuery = trim($ticket->subject . ' ' . $ticket->description);
        $normalized = $this->normalizeQuery($rawQuery);

        if ($normalized === '') {
            return collect();
        }

        return HelpdeskTicket::query()
            ->with('reporter')
            ->select('helpdesk_tickets.*')
            ->selectRaw('MATCH(helpdesk_tickets.subject, helpdesk_tickets.description) AGAINST (? IN NATURAL LANGUAGE MODE) AS fulltext_score', [$normalized])
            ->where('helpdesk_tickets.id', '!=', $ticket->id)
            ->where('helpdesk_tickets.status', '!=', \App\Enums\TicketStatus::CLOSED)
            ->whereNull('helpdesk_tickets.duplicate_of_id')
            ->having('fulltext_score', '>=', $threshold)
            ->orderByDesc('fulltext_score')
            ->limit($limit)
            ->get();
    }
}

