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

    /** @var array<int, array{id: int, subject: string, snippet: string, status: string, match: int, is_closed: bool}> */
    public array $similarTickets = [];

    public function updatedSubject(): void
    {
        $this->searchSimilar();
    }

    public function searchSimilar(): void
    {
        $term = trim($this->subject);
        $words = preg_split('/\s+/', $term);

        if (count($words) < 3 || mb_strlen($term) < 8) {
            $this->similarTickets = [];
            return;
        }

        // Cari aduan rujukan (is_reference = true) atau tiket in_progress/closed
        // Catatan: DuplicateTicketFinder penuh akan disempurnakan di Fase 6
        $query = HelpdeskTicket::query()
            ->where(function ($q) {
                $q->where('is_reference', true)
                  ->orWhere('status', TicketStatus::IN_PROGRESS);
            })
            ->where(function ($q) use ($term) {
                $q->where('subject', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            })
            ->limit(3)
            ->get();

        $this->similarTickets = $query->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'snippet' => mb_substr($ticket->description, 0, 140) . '...',
                'status' => $ticket->status->value,
                'match' => 85,
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

        // Hitung tenggat SLA dari kebijakan
        $policy = SlaPolicy::forPriority($priorityEnum);
        $responseMinutes = $policy ? $policy->response_minutes : 60;
        $resolutionMinutes = $policy ? $policy->resolution_minutes : 1440;

        $now = now();
        $ticket = HelpdeskTicket::create([
            'reporter_id' => $user->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $priorityEnum,
            'status' => TicketStatus::OPEN,
            'response_due_at' => (clone $now)->addMinutes($responseMinutes),
            'resolution_due_at' => (clone $now)->addMinutes($resolutionMinutes),
        ]);

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
