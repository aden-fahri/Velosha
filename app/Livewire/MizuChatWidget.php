<?php

namespace App\Livewire;

use App\Support\MizuAiService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MizuChatWidget extends Component
{
    /** Jumlah maksimal pesan dalam satu sesi obrolan. */
    public const MESSAGE_LIMIT = 20;

    /** @var array<int, array{role: string, text: string}> */
    public array $messages = [];

    public string $notice = '';

    #[Validate('nullable|string|max:500')]
    public string $question = '';

    public function ask(): void
    {
        $this->validate();

        $text = trim($this->question);

        if ($text === '') {
            return;
        }

        $this->question = '';
        $this->pushAndReply($text);
    }

    public function askQuick(string $question): void
    {
        $this->question = '';
        $this->pushAndReply($question);
    }

    public function resetChat(): void
    {
        $this->reset('messages', 'question', 'notice');
    }

    protected function pushAndReply(string $text): void
    {
        $id = auth()->id() ?: request()->ip();
        $minKey = 'mizu-min:'.$id;
        $dayKey = 'mizu-day:'.$id;
        if (RateLimiter::tooManyAttempts($minKey, 10) || RateLimiter::tooManyAttempts($dayKey, 50)) {
            $this->notice = 'Terlalu banyak permintaan ke Mizu. Coba lagi nanti.';

            return;
        }
        RateLimiter::hit($minKey, 60);
        RateLimiter::hit($dayKey, 86400);

        if (count($this->messages) >= self::MESSAGE_LIMIT) {
            $this->messages = [];
            $this->notice = 'Sesi obrolan sudah panjang, Mizu memulai dari awal.';
        }

        $this->messages[] = ['role' => 'user', 'text' => $text];

        $answer = app(MizuAiService::class)->ask($this->messages);

        $this->messages[] = ['role' => 'model', 'text' => $answer];

        // Beri tahu Alpine untuk menggulir ke pesan terbaru.
        $this->dispatch('mizu-answered');
    }

    public function render()
    {
        return view('livewire.mizu-chat-widget', [
            'quickPrompts' => [
                'Syarat ambil Race Pack?',
                'Apa itu Cut-Off Time (COT)?',
                'Cara cek nomor BIB di tiket?',
            ],
        ]);
    }
}