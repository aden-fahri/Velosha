<?php

namespace App\Support;

use App\Models\AboutContent;
use App\Models\HelpContent;
use App\Models\RuleContent;
use App\Models\TicketPackageContent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Client REST Google Gemini untuk asisten AI "Tanya Mizu".
 *
 * Free tier resmi Google AI Studio (tanpa kartu kredit). Dipanggil lewat
 * komponen Livewire MizuChatWidget. Basis pengetahuan disusun dari konten
 * situs yang tersimpan di database, sehingga tetap sinkron dengan perubahan
 * yang dilakukan pengelola lewat panel.
 */
class MizuAiService
{
    /** Jumlah pesan terakhir yang dikirim ke model (hemat kuota free tier). */
    public const HISTORY_LIMIT = 8;

    /** Batas panjang pertanyaan pengguna. */
    public const INPUT_LIMIT = 500;

    /** Batas panjang balasan model. */
    public const OUTPUT_LIMIT = 1200;

    public function isConfigured(): bool
    {
        return filled($this->apiKey());
    }

    /**
     * Kirim riwayat percakapan ke Gemini dan kembalikan teks jawabannya.
     *
     * @param  array<int, array{role: string, text: string}>  $history
     */
    public function ask(array $history): string
    {
        if (! $this->isConfigured()) {
            return 'Halo! Mizu belum siap beroperasi karena kunci API belum dikonfigurasi.';
        }

        $contents = $this->formatHistory($history);

        if ($contents === []) {
            return 'Tulis dulu pertanyaannya ya, Mizu siap membantu.';
        }

        try {
            $response = Http::acceptJson()
                ->timeout(20)
                ->retry(1, 200, throw: false)
                ->post($this->endpoint(), [
                    'systemInstruction' => [
                        'parts' => [['text' => $this->systemPrompt()]],
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'maxOutputTokens' => 800,
                    ],
                ]);
        } catch (Throwable $exception) {
            Log::error('Mizu — gagal menghubungi Gemini: '.$exception->getMessage());

            return 'Maaf, Mizu sedang kesulitan menghubungi layanan AI. Coba lagi beberapa saat lagi ya.';
        }

        if (! $response->successful()) {
            Log::error('Mizu — Gemini API error '.$response->status().': '.mb_substr($response->body(), 0, 500));

            return 'Layanan AI sedang sibuk. Silakan coba beberapa saat lagi.';
        }

        $answer = $response->json('candidates.0.content.parts.0.text');

        if (! is_string($answer) || trim($answer) === '') {
            $blockReason = $response->json('promptFeedback.blockReason');
            Log::warning('Mizu — jawaban kosong dari Gemini.'.($blockReason ? ' blockReason: '.$blockReason : ''));

            return 'Maaf, Mizu belum bisa menjawab pertanyaan itu. Coba dengan kalimat lain.';
        }

        return $this->cleanAnswer($answer);
    }

    /**
     * Potong histori ke percakapan terakhir yang wajar sebelum dikirim.
     *
     * @param  array<int, array{role: string, text: string}>  $history
     * @return array<int, array{role: string, parts: array<int, array{text: string}>}>
     */
    protected function formatHistory(array $history): array
    {
        $clean = [];

        foreach ($history as $message) {
            $role = $message['role'] ?? null;
            $text = trim((string) ($message['text'] ?? ''));

            if ($role !== 'user' && $role !== 'model') {
                continue;
            }

            if ($text === '') {
                continue;
            }

            if ($role === 'user') {
                $text = mb_substr($text, 0, self::INPUT_LIMIT);
            }

            $clean[] = ['role' => $role, 'text' => $text];
        }

        return array_map(
            fn (array $message): array => [
                'role' => $message['role'],
                'parts' => [['text' => $message['text']]],
            ],
            array_slice($clean, -self::HISTORY_LIMIT)
        );
    }

    /**
     * Bersihkan balasan model dari markdown berat dan potong panjangnya.
     */
    protected function cleanAnswer(string $answer): string
    {
        $answer = preg_replace("/```[a-zA-Z]*\n?/", '', $answer) ?? $answer;
        $answer = trim($answer);

        if (mb_strlen($answer) > self::OUTPUT_LIMIT) {
            $answer = rtrim(mb_substr($answer, 0, self::OUTPUT_LIMIT)).'…';
        }

        return $answer;
    }

    protected function apiKey(): string
    {
        return trim((string) config('services.gemini.key'));
    }

    protected function model(): string
    {
        return trim((string) config('services.gemini.model')) ?: 'gemini-3.1-flash-lite';
    }

    protected function endpoint(): string
    {
        $base = rtrim((string) config('services.gemini.url'), '/');

        return $base.'/'.$this->model().':generateContent?key='.$this->apiKey();
    }

    /**
     * System prompt Mizu: persona + pengetahuan yang diambil dari database.
     */
    protected function systemPrompt(): string
    {
        return implode("\n\n", array_filter([
            $this->persona(),
            $this->glossaryKnowledge(),
            $this->regulationKnowledge(),
            $this->packageKnowledge(),
            $this->aboutKnowledge(),
            $this->answerRules(),
        ]));
    }

    protected function persona(): string
    {
        return <<<'TEXT'
        Kamu adalah Mizu, asisten AI resmi dari Velosha — platform kurasi event balap sepeda, Gran Fondo, dan endurance ride di Indonesia.

        Kepribadian:
        - Ramah, antusias, dan suportif terhadap komunitas gowes.
        - Bahasa Indonesia yang luwes, hangat, dan mudah dipahami (bukan kaku seperti dokumen).
        - Ringkas: jawab inti pertanyaannya dulu, tambah konteks seperlunya.
        TEXT;
    }

    protected function answerRules(): string
    {
        return <<<'TEXT'
        Aturan menjawab:
        1. Gunakan hanya informasi dari pengetahuan yang diberikan. Jangan mengarang nomor telepon, tanggal, harga, atau aturan yang tidak ada di sana.
        2. Jika jawaban tidak ada dalam pengetahuan, katakan dengan jujur bahwa informasinya belum tersedia, lalu arahkan pengguna ke halaman Help Center. Jangan mengarang.
        3. Jika ditanya di luar topik balap/sepeda/Velosha, jawab singkat lalu arahkan kembali ke topik pendaftaran dan balapan.
        4. Jangan pernah memberi instruksi yang bertentangan dengan regulasi resmi Velosha.
        5. Format jawaban singkat dengan paragraf atau poin bila perlu. Jangan gunakan markdown tebal berlebihan.
        TEXT;
    }

    /**
     * Glosarium istilah dan FAQ dari konten Help Center.
     */
    protected function glossaryKnowledge(): string
    {
        $help = HelpContent::current();
        $groups = $help->faq_groups ?? [];

        if (! is_array($groups) || $groups === []) {
            return '';
        }

        $lines = [];

        foreach ($groups as $group) {
            $title = trim((string) ($group['title'] ?? ''));
            $items = $group['items'] ?? [];

            if ($title !== '' && is_array($items) && $items !== []) {
                $lines[] = '## '.$title;
            }

            foreach ((array) $items as $item) {
                $question = trim((string) ($item['q'] ?? ''));
                $answer = trim((string) ($item['a'] ?? ''));

                if ($question !== '' && $answer !== '') {
                    $lines[] = '- '.$question.' '.$answer;
                }
            }
        }

        return $lines === [] ? '' : "## Pengetahuan: FAQ & Istilah Sepeda\n".implode("\n", $lines);
    }

    /**
     * Regulasi balapan dari konten Rule Center.
     */
    protected function regulationKnowledge(): string
    {
        $rule = RuleContent::current();
        $lines = [];
        $statusLabel = [
            'allowed' => 'diizinkan',
            'mandatory' => 'wajib',
            'forbidden' => 'dilarang',
            'note' => 'catatan',
        ];

        foreach (($rule->groups ?? []) as $group) {
            $title = trim((string) ($group['title'] ?? ''));

            if ($title !== '' && $title !== '0') {
                $lines[] = '### '.$title;
            }

            foreach ((array) ($group['items'] ?? []) as $item) {
                $label = trim((string) ($item['label'] ?? ''));
                $text = trim((string) ($item['text'] ?? ''));
                $status = $statusLabel[$item['status'] ?? ''] ?? 'catatan';

                if ($label !== '' && $text !== '') {
                    $lines[] = '- '.$label.' ('.$status.'): '.$text;
                }
            }
        }

        foreach (($rule->sanksi_items ?? []) as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            $text = trim((string) ($item['text'] ?? ''));

            if ($title !== '' && $text !== '') {
                $lines[] = 'Sanksi — '.$title.': '.$text;
            }
        }

        $note = trim((string) ($rule->sanksi_note ?? ''));

        if ($note !== '') {
            $lines[] = 'Catatan sanksi: '.$note;
        }

        return $lines === [] ? '' : "## Pengetahuan: Regulasi Balapan\n".implode("\n", $lines);
    }

    /**
     * Isi paket yang didapat peserta dari satu tiket.
     */
    protected function packageKnowledge(): string
    {
        $package = TicketPackageContent::current();
        $lines = [];

        foreach (($package->features ?? []) as $feature) {
            $title = trim((string) ($feature['title'] ?? ''));
            $desc = trim((string) ($feature['desc'] ?? ''));

            if ($title !== '' && $desc !== '') {
                $lines[] = '- '.$title.': '.$desc;
            }
        }

        foreach (($package->benefits ?? []) as $benefit) {
            $text = trim((string) ($benefit['text'] ?? ''));

            if ($text !== '') {
                $lines[] = '- '.$text;
            }
        }

        return $lines === [] ? '' : "## Pengetahuan: Isi Paket Tiket\n".implode("\n", $lines);
    }

    protected function aboutKnowledge(): string
    {
        $about = AboutContent::current();
        $parts = array_filter([
            trim((string) ($about->header_title ?? '')),
            trim((string) ($about->header_description ?? '')),
        ]);

        return $parts === []
            ? ''
            : "## Pengetahuan: Tentang Velosha\n".implode("\n", $parts);
    }
}