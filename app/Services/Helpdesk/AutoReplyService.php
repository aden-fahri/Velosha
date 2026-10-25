<?php

namespace App\Services\Helpdesk;

use App\Enums\Helpdesk\TicketCategory;
use App\Models\HelpdeskTicket;
use App\Models\ReplyTemplate;
use App\Models\TicketResponse;
use App\Support\Helpdesk\SystemUser;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Layanan untuk pembuatan balasan otomatis (auto-reply) dan rekomendasi tanggapan operator.
 *
 * Mengimplementasikan aturan ketat non-empty guarantee:
 * pesan kosong atau yang menjadi kosong setelah substitusi placeholder tidak akan pernah
 * disimpan ke tabel ticket_responses.
 */
class AutoReplyService
{
    /**
     * Membangun teks balasan dari template dengan mensubstitusi placeholder.
     *
     * Placeholder yang didukung:
     * - {{reporter_name}} : Nama pelapor tiket
     * - {{ticket_id}}      : Nomor id tiket dalam format "#<id>"
     * - {{ticket_category}}: Nama kategori tiket
     *
     * Placeholder yang tidak dikenal akan dibuang (dikosongkan), tidak dicetak mentah.
     * Jika setelah pemangkasan spasi hasilnya kosong (blank, whitespace, "\n\t"),
     * metode ini mengembalikan null.
     *
     * @param ReplyTemplate|string|null $template Entitas template atau string mentah template
     * @param HelpdeskTicket $ticket Tiket acuan data placeholder
     * @return string|null Hasil teks pesan yang sudah diproses atau null jika kosong
     */
    public function build(ReplyTemplate|string|null $template, HelpdeskTicket $ticket): ?string
    {
        if ($template === null) {
            return null;
        }

        $rawBody = $template instanceof ReplyTemplate ? $template->body : $template;

        if (trim($rawBody) === '') {
            return null;
        }

        // Siapkan nilai substitusi
        $reporterName = (string) ($ticket->reporter?->name ?? '');
        $ticketId = '#' . $ticket->id;
        $categoryValue = $ticket->category instanceof TicketCategory
            ? $ticket->category->value
            : (string) ($ticket->category ?? '');

        $replacements = [
            'reporter_name' => $reporterName,
            'ticket_id' => $ticketId,
            'ticket_category' => $categoryValue,
        ];

        // Ganti placeholder yang dikenal, atau kosongkan jika tidak dikenal
        $processed = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($matches) use ($replacements) {
            $key = $matches[1];
            return $replacements[$key] ?? '';
        }, $rawBody);

        $trimmed = trim((string) $processed);

        return $trimmed !== '' ? $trimmed : null;
    }

    /**
     * Mengirimkan balasan otomatis sistem pada tiket.
     *
     * Memilih template terbaik jika parameter template tidak diberikan.
     * Menjamin tidak membuat baris ticket_responses jika hasil build bernilai null.
     * Tiket tetap berstatus Open dan first_response_at tetap null.
     *
     * @param HelpdeskTicket $ticket Tiket target balasan
     * @param ReplyTemplate|string|null $template Template spesifik (opsional)
     * @return TicketResponse|null Entitas tanggapan tiket yang dibuat, atau null jika tidak ada pesan
     */
    public function send(HelpdeskTicket $ticket, ReplyTemplate|string|null $template = null): ?TicketResponse
    {
        if ($template === null) {
            $template = $this->getSuggestions($ticket, 1)->first();
        }

        if ($template === null) {
            return null;
        }

        $message = $this->build($template, $ticket);

        if ($message === null) {
            return null;
        }

        $botUser = SystemUser::get();

        return TicketResponse::create([
            'ticket_id' => $ticket->id,
            'responder_id' => $botUser->id,
            'message' => $message,
            'is_auto_reply' => true,
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * Mengambil rekomendasi saran balasan cepat untuk operator berdasarkan kategori dan kata kunci.
     *
     * Algoritma penilaian:
     * - Kesesuaian Kategori : +10 poin
     * - Kesesuaian Kata Kunci: +2 poin per kecocokan kata kunci pada subjek atau deskripsi
     *
     * Pemecah seri deterministik (deterministic tie-break):
     * 1. Skor relevansi tertinggi (score DESC)
     * 2. Frekuensi penggunaan terbanyak (use_count DESC)
     * 3. ID terkecil (id ASC)
     *
     * Saran yang dikembalikan murni berasal dari reply_templates yang dikonfigurasi sistem,
     * sehingga tidak pernah memuat data pribadi pelapor lain.
     *
     * @param HelpdeskTicket $ticket Tiket yang sedang ditangani operator
     * @param int $limit Jumlah maksimum rekomendasi yang dikembalikan (default 3)
     * @return Collection<int, ReplyTemplate> Koleksi template yang diurutkan
     */
    public function getSuggestions(HelpdeskTicket $ticket, int $limit = 3): Collection
    {
        $templates = ReplyTemplate::where('is_active', true)->get();

        if ($templates->isEmpty()) {
            return collect();
        }

        $ticketText = mb_strtolower($ticket->subject . ' ' . $ticket->description);
        $ticketCategoryVal = $ticket->category instanceof TicketCategory
            ? $ticket->category->value
            : (string) ($ticket->category ?? '');

        $scored = $templates->map(function (ReplyTemplate $tpl) use ($ticketText, $ticketCategoryVal) {
            $score = 0;
            $tplCategoryVal = $tpl->category instanceof TicketCategory
                ? $tpl->category->value
                : (string) ($tpl->category ?? '');

            if ($tplCategoryVal === $ticketCategoryVal && $ticketCategoryVal !== '') {
                $score += 10;
            }

            foreach ($tpl->getKeywordList() as $keyword) {
                if ($keyword !== '' && mb_stripos($ticketText, mb_strtolower($keyword)) !== false) {
                    $score += 2;
                }
            }

            $tpl->suggestion_score = $score;
            return $tpl;
        });

        // Urutkan secara deterministik
        $sorted = $scored->sort(function (ReplyTemplate $a, ReplyTemplate $b) {
            if ($a->suggestion_score !== $b->suggestion_score) {
                return $b->suggestion_score <=> $a->suggestion_score;
            }

            if ($a->use_count !== $b->use_count) {
                return $b->use_count <=> $a->use_count;
            }

            return $a->id <=> $b->id;
        });

        // Filter saran relevan jika tersedia yang memiliki skor > 0
        $hasRelevant = $sorted->contains(fn ($tpl) => $tpl->suggestion_score > 0);
        $filtered = $hasRelevant ? $sorted->filter(fn ($tpl) => $tpl->suggestion_score > 0) : $sorted;

        return $filtered->take($limit)->values();
    }

    /**
     * Mencatat penggunaan template secara atomik dengan menaikkan kolom use_count.
     *
     * @param int|ReplyTemplate $template ID atau instance ReplyTemplate
     * @return void
     */
    public function recordTemplateUsage(int|ReplyTemplate $template): void
    {
        $id = $template instanceof ReplyTemplate ? $template->id : (int) $template;
        ReplyTemplate::where('id', $id)->increment('use_count');
    }
}
