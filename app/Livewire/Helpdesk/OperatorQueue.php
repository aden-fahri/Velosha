<?php

namespace App\Livewire\Helpdesk;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\Helpdesk\TicketService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['nav' => false])]
#[Title('Antrian Tiket Operator — Velosha Helpdesk')]
class OperatorQueue extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all, open, in_progress, closed, my_tickets
    public string $search = '';

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->isOperator() && ! $user->isAdmin())) {
            abort(403, 'Akses ditolak. Halaman ini khusus staf helpdesk dan administrator.');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function claimTicket(int $ticketId): void
    {
        $ticket = HelpdeskTicket::find($ticketId);
        if (! $ticket) {
            session()->flash('error', 'Tiket tidak ditemukan.');
            return;
        }

        try {
            $service = app(TicketService::class);
            $service->claimTicket($ticket, Auth::user());
            session()->flash('success', "Tiket #{$ticket->id} berhasil diklaim dan masuk antrean Anda.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            session()->flash('error', 'Anda tidak berwenang mengklaim tiket ini.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 4 KPI Counters
        $kpiOpen = HelpdeskTicket::where('status', TicketStatus::OPEN)->count();
        $kpiInProgress = HelpdeskTicket::where('status', TicketStatus::IN_PROGRESS)->count();

        $kpiBreached = HelpdeskTicket::whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS])
            ->where(function ($q) use ($now) {
                $q->where(function ($sq) use ($now) {
                    $sq->whereNull('first_response_at')
                       ->whereNotNull('response_due_at')
                       ->where('response_due_at', '<', $now);
                })->orWhere(function ($sq) use ($now) {
                    $sq->whereNotNull('resolution_due_at')
                       ->where('resolution_due_at', '<', $now);
                });
            })->count();

        $kpiClosedToday = HelpdeskTicket::where('status', TicketStatus::CLOSED)
            ->whereDate('closed_at', Carbon::today())
            ->count();

        // Query Daftar Antrian
        $query = HelpdeskTicket::with(['reporter', 'operator']);

        if ($this->search !== '') {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', $s)
                  ->orWhere('id', 'like', $s)
                  ->orWhereHas('reporter', function ($sq) use ($s) {
                      $sq->where('name', 'like', $s)
                         ->orWhere('email', 'like', $s);
                  });
            });
        }

        match ($this->filter) {
            'open' => $query->where('status', TicketStatus::OPEN),
            'in_progress' => $query->where('status', TicketStatus::IN_PROGRESS),
            'closed' => $query->where('status', TicketStatus::CLOSED),
            'my_tickets' => $query->where('operator_id', $user?->id),
            default => null,
        };

        $tickets = $query->orderByRaw("
            CASE 
                WHEN status = 'Open' THEN 1
                WHEN status = 'In-Progress' THEN 2
                ELSE 3
            END ASC
        ")->latest('created_at')->paginate(10);

        return view('livewire.helpdesk.operator-queue', [
            'kpiOpen' => $kpiOpen,
            'kpiInProgress' => $kpiInProgress,
            'kpiBreached' => $kpiBreached,
            'kpiClosedToday' => $kpiClosedToday,
            'tickets' => $tickets,
        ]);
    }
}
