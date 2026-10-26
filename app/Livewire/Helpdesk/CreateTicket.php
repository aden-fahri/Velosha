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

/**
 * @brief Komponen Livewire pembuatan tiket bantuan baru oleh pelapor.
 */
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

    #[Validate('required|string|in:Tiket/QR,Pembayaran,Akun,Lokasi,Lainnya', message: [
        'required' => 'Kategori kendala wajib dipilih.',
        'in' => 'Kategori yang dipilih tidak valid.',
    ])]
    public string $category = 'Tiket/QR';

    #[Validate('required|string|min:10|max:5000', message: [
        'required' => 'Deskripsi kendala wajib diisi.',
        'min' => 'Deskripsi minimal 10 karakter.',
        'max' => 'Deskripsi maksimal 5000 karakter.',
    ])]
    public string $description = '';

    public string $priority = 'Mid';

    /**
     * Rekomendasi tiket serupa hasil pencarian duplicate finder.
     * @var array $similarTickets
     */
    public array $similarTickets = [];

    public ?string $feedbackMessage = null;

    public function updatedSubject(): void
    {
        $this->searchSimilar();
    }

    public function updatedDescription(): void
    {
        $this->searchSimilar();
    }

    public function searchSimilar(): void
    {
        $term = trim($this->subject . ' ' . $this->description);
        $words = preg_split('/\s+/u', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($words) < 3) {
            $this->similarTickets = [];
            return;
        }

        $user = Auth::user();
        if (! $user) {
            $this->similarTickets = [];
            return;
        }

        $results = app(\App\Services\Helpdesk\DuplicateTicketFinder::class)->findSimilar(
            $user,
            $this->subject,
            $this->description,
            3
        );

        $this->similarTickets = $results->map(function (HelpdeskTicket $ticket) {
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'solution_summary' => $ticket->solution_summary,
                'is_own_ticket' => (bool) ($ticket->is_own_ticket ?? false),
                'is_closed' => $ticket->isClosed(),
                'status' => $ticket->status instanceof TicketStatus ? $ticket->status->value : (string) $ticket->status,
            ];
        })->toArray();
    }

    public function markAnswered(int $ticketId): void
    {
        $user = Auth::user();
        if ($user) {
            \App\Models\TicketSuggestionFeedback::updateOrCreate(
                ['ticket_id' => $ticketId, 'reporter_id' => $user->id],
                ['helpful' => true, 'created_at' => now()]
            );
        }

        $this->feedbackMessage = 'Terima kasih atas tanggapan Anda! Senang kendala Anda sudah terjawab tanpa perlu membuka tiket baru.';
        $this->reset(['subject', 'description', 'similarTickets']);
    }

    public function rejectAnswerAndSubmit(int $ticketId): void
    {
        $user = Auth::user();
        if ($user) {
            \App\Models\TicketSuggestionFeedback::updateOrCreate(
                ['ticket_id' => $ticketId, 'reporter_id' => $user->id],
                ['helpful' => false, 'created_at' => now()]
            );
        }

        $this->submit();
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
        $categoryEnum = \App\Enums\Helpdesk\TicketCategory::tryFrom($this->category) ?? \App\Enums\Helpdesk\TicketCategory::LAINNYA;

        $ticketService = app(\App\Services\Helpdesk\TicketService::class);
        $ticket = $ticketService->createTicket($user, [
            'category' => $categoryEnum,
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
