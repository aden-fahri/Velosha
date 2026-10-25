<?php

namespace App\Livewire\Helpdesk;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\SlaPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app', ['nav' => false])]
#[Title('Buat Tiket Bantuan — Velosha')]
class CreateTicket extends Component
{
    #[Validate('required|string|min:5|max:160', message: [
        'required' => 'Subjek tiket wajib diisi.',
        'min' => 'Subjek minimal 5 karakter.',
        'max' => 'Subjek maksimal 160 karakter.',
    ])]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000', message: [
        'required' => 'Deskripsi kendala wajib diisi.',
        'min' => 'Deskripsi minimal 10 karakter.',
        'max' => 'Deskripsi maksimal 5000 karakter.',
    ])]
    public string $description = '';

    public string $priority = 'Mid';

    /** @var array<int, array{id: int, subject: string, solution_summary: string, is_closed: bool}> */
    public array $similarTickets = [];

    public function updatedSubject(): void
    {
        $this->searchSimilar();
    }

    public function searchSimilar(): void
    {
        $term = trim($this->subject);
        $words = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($words) < 3 || mb_strlen($term) < 8) {
            $this->similarTickets = [];
            return;
        }

        $userId = Auth::id();

        // Aturan Privasi Ketat:
        // 1. HANYA tiket rujukan (is_reference = true) DAN memiliki solution_summary terisi.
        // 2. Tiket milik pelapor sendiri dikecualikan.
        // 3. Tiket In-Progress biasa milik pelapor lain TIDAK PERNAH muncul.
        // 4. Pencocokan per kata (per-word matching).
        $query = HelpdeskTicket::query()
            ->where('is_reference', true)
            ->whereNotNull('solution_summary')
            ->where('solution_summary', '!=', '');

        if ($userId) {
            $query->where('reporter_id', '!=', $userId);
        }

        // Per-word matching pada subjek atau solution_summary
        $query->where(function ($q) use ($words) {
            foreach ($words as $word) {
                if (mb_strlen($word) >= 3) {
                    $q->orWhere('subject', 'like', "%{$word}%")
                      ->orWhere('solution_summary', 'like', "%{$word}%");
                }
            }
        });

        $results = $query->limit(3)->get();

        $this->similarTickets = $results->map(function (HelpdeskTicket $ticket) {
            return [
                'id' => $ticket->id,
                'subject' => \App\Support\Helpdesk\TicketSanitizer::clean($ticket->subject),
                'solution_summary' => \App\Support\Helpdesk\TicketSanitizer::clean($ticket->solution_summary),
                'is_closed' => $ticket->isClosed(),
            ];
        })->toArray();
    }

    public function submit(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Rate limit: 5 pembuatan tiket per jam per pengguna
        $rateKey = 'helpdesk-create:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $this->addError('subject', "Terlalu banyak tiket baru. Silakan tunggu {$seconds} detik lagi.");
            return;
        }

        $this->validate();

        $priorityEnum = TicketPriority::tryFrom($this->priority) ?? TicketPriority::MID;

        $ticketService = app(\App\Services\Helpdesk\TicketService::class);
        $ticket = $ticketService->createTicket($user, [
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $priorityEnum,
        ], now());

        RateLimiter::hit($rateKey, 3600);

        session()->flash('success', 'Tiket bantuan berhasil dibuat. Pengelola akan segera merespons.');

        $this->redirect(route('helpdesk.my-tickets', ['ticket' => $ticket->id]), navigate: true);
    }

    public function render()
    {
        $activeTicketCount = HelpdeskTicket::where('reporter_id', Auth::id())
            ->whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS])
            ->count();

        return view('livewire.helpdesk.create-ticket', [
            'activeTicketCount' => $activeTicketCount,
        ]);
    }
}
