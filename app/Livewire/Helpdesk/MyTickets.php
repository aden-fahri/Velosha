<?php

namespace App\Livewire\Helpdesk;

use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\SatisfactionRating;
use App\Models\TicketResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app', ['nav' => false])]
#[Title('Tiket Saya — Velosha Helpdesk')]
class MyTickets extends Component
{
    public string $filter = 'all'; // 'all', 'Open', 'Closed'

    #[Locked]
    public ?int $selectedTicketId = null;

    #[Validate('required|string|min:2|max:5000', message: [
        'required' => 'Pesan balasan wajib diisi.',
        'min' => 'Pesan balasan minimal 2 karakter.',
        'max' => 'Pesan balasan maksimal 5000 karakter.',
    ])]
    public string $replyMessage = '';

    // Form penilaian (rating)
    public int $ratingScore = 5;
    public string $ratingFeedback = '';
    public bool $ratingSubmitted = false;

    public function mount(?int $ticket = null): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if ($ticket) {
            $this->selectTicket($ticket);
        } else {
            // Default: pilih tiket terbaru milik user
            $latest = HelpdeskTicket::where('reporter_id', $user->id)->latest()->first();
            if ($latest) {
                $this->selectedTicketId = $latest->id;
            }
        }
    }

    public function setFilter(string $filter): void
    {
        if (in_array($filter, ['all', 'Open', 'Closed'], true)) {
            $this->filter = $filter;
        }
    }

    public function selectTicket(int $ticketId): void
    {
        $ticket = HelpdeskTicket::where('id', $ticketId)->first();

        if (! $ticket) {
            abort(404, 'Tiket tidak ditemukan.');
        }

        // Isolasi pelapor: jika bukan tiket miliknya dan bukan admin/operator -> 404
        if ($ticket->reporter_id !== Auth::id() && ! Auth::user()->isAdmin() && ! Auth::user()->isOperator()) {
            abort(404, 'Tiket tidak ditemukan.');
        }

        $this->selectedTicketId = $ticket->id;
        $this->replyMessage = '';
        $this->ratingFeedback = '';
        $this->ratingSubmitted = false;
        $this->resetValidation();
    }

    public function sendReply(): void
    {
        $user = Auth::user();
        if (! $user || ! $this->selectedTicketId) {
            return;
        }

        $ticket = HelpdeskTicket::where('id', $this->selectedTicketId)->first();
        if (! $ticket) {
            abort(404);
        }

        if (! Gate::forUser($user)->allows('reply', $ticket)) {
            $this->addError('replyMessage', 'Tiket sudah ditutup dan tidak dapat menerima balasan lagi.');
            return;
        }

        // Rate limit: 20 balasan per jam
        $rateKey = 'helpdesk-reply:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateKey, 20)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $this->addError('replyMessage', "Terlalu banyak balasan. Silakan tunggu {$seconds} detik lagi.");
            return;
        }

        app(\App\Services\Helpdesk\TicketService::class)->addReply($ticket, $user, trim($this->replyMessage));

        RateLimiter::hit($rateKey, 3600);

        $this->replyMessage = '';
        $this->dispatch('ticket-replied');
    }

    public function submitRating(): void
    {
        $user = Auth::user();
        if (! $user || ! $this->selectedTicketId) {
            return;
        }

        $ticket = HelpdeskTicket::where('id', $this->selectedTicketId)->first();
        if (! $ticket) {
            abort(404);
        }

        if (! Gate::forUser($user)->allows('rate', $ticket)) {
            $this->addError('ratingFeedback', 'Penilaian hanya dapat diberikan satu kali untuk tiket yang sudah Closed.');
            return;
        }

        $score = max(1, min(5, $this->ratingScore));

        SatisfactionRating::create([
            'ticket_id' => $ticket->id,
            'score' => $score,
            'feedback' => trim($this->ratingFeedback) ?: null,
            'created_at' => now(),
        ]);

        $this->ratingSubmitted = true;
        session()->flash('rating_success', 'Terima kasih atas penilaian Anda!');
    }

    public function render()
    {
        $user = Auth::user();
        $query = HelpdeskTicket::where('reporter_id', $user->id);

        if ($this->filter === 'Open') {
            $query->whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS]);
        } elseif ($this->filter === 'Closed') {
            $query->where('status', TicketStatus::CLOSED);
        }

        $tickets = $query->latest()->get();

        $selectedTicket = null;
        if ($this->selectedTicketId) {
            $selectedTicket = HelpdeskTicket::with(['responses.responder', 'operator', 'rating'])
                ->where('id', $this->selectedTicketId)
                ->where('reporter_id', $user->id)
                ->first();
        }

        $activeTicketCount = HelpdeskTicket::where('reporter_id', $user->id)
            ->whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS])
            ->count();

        return view('livewire.helpdesk.my-tickets', [
            'tickets' => $tickets,
            'selectedTicket' => $selectedTicket,
            'activeTicketCount' => $activeTicketCount,
        ]);
    }
}
