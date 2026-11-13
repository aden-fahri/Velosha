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

/**
 * @brief Komponen Livewire dasbor dan antrian penanganan tiket masuk untuk operator helpdesk.
 */
#[Layout('layouts.app', ['nav' => false])]
#[Title('Dasbor Operator & Helpdesk — Velosha')]
class OperatorQueue extends Component
{
    use WithPagination;

    public string $tab = 'queue'; // 'overview', 'queue', 'profile'
    public string $filter = 'all'; // all, open, in_progress, closed, my_tickets
    public string $priorityFilter = 'all'; // all, High, Mid, Low
    public string $categoryFilter = 'all'; // all, Pendaftaran, Pembayaran, Teknis, Penyelenggara
    public string $search = '';

    // Profile form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $profileSaved = false;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->isOperator() && ! $user->isAdmin())) {
            abort(403, 'Akses ditolak. Halaman ini khusus staf helpdesk dan administrator.');
        }

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';

        if (request()->has('filter')) {
            $this->filter = request()->query('filter', 'all');
            $this->tab = 'queue';
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->profileSaved = false;
        $this->resetPage();
    }

    public function updateProfile(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
        ];

        if (! empty($this->new_password)) {
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $this->validate($rules);

        $user->name = $this->name;
        $user->phone = $this->phone;

        if (! empty($this->new_password)) {
            $user->password = \Illuminate\Support\Facades\Hash::make($this->new_password);
        }

        $user->save();

        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->profileSaved = true;
        session()->flash('success', 'Profil petugas helpdesk berhasil diperbarui!');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->tab = 'queue';
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $this->categoryFilter = $category;
        $this->resetPage();
    }

    public function setPriority(string $priority): void
    {
        $this->priorityFilter = $priority;
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

        $myActiveTickets = HelpdeskTicket::where('operator_id', $user?->id)
            ->whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS])
            ->count();

        // Ringkasan Dasbor: 6 Tiket Mendesak (hanya dimuat saat tab overview)
        $urgentTickets = collect();
        if ($this->tab === 'overview') {
            $urgentTickets = HelpdeskTicket::with(['reporter:id,name,email', 'operator:id,name'])
                ->whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS])
                ->orderByRaw("
                    CASE priority 
                        WHEN 'High' THEN 1 
                        WHEN 'Mid' THEN 2 
                        WHEN 'Low' THEN 3 
                        ELSE 4 
                    END ASC
                ")->orderBy('created_at', 'asc')
                ->take(6)
                ->get();
        }

        // Query Antrian Penuh (hanya dimuat saat tab queue)
        $tickets = null;
        if ($this->tab === 'queue') {
            $query = HelpdeskTicket::with(['reporter:id,name,email', 'operator:id,name']);

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

            if ($this->priorityFilter !== 'all' && in_array($this->priorityFilter, ['High', 'Mid', 'Low'], true)) {
                $query->where('priority', $this->priorityFilter);
            }

            if ($this->categoryFilter !== 'all') {
                $query->where('category', 'like', '%' . $this->categoryFilter . '%');
            }

            $tickets = $query->orderByRaw("
                CASE 
                    WHEN status IN ('Open', 'In-Progress') THEN 0 
                    ELSE 1 
                END ASC
            ")->orderByRaw("
                CASE priority 
                    WHEN 'High' THEN 1 
                    WHEN 'Mid' THEN 2 
                    WHEN 'Low' THEN 3 
                    ELSE 4 
                END ASC
            ")->orderBy('created_at', 'asc')
              ->orderBy('id', 'asc')
              ->paginate(12);
        } else {
            $tickets = HelpdeskTicket::whereRaw('1 = 0')->paginate(12);
        }

        return view('livewire.helpdesk.operator-queue', [
            'kpiOpen' => $kpiOpen,
            'kpiInProgress' => $kpiInProgress,
            'kpiBreached' => $kpiBreached,
            'kpiClosedToday' => $kpiClosedToday,
            'myActiveTickets' => $myActiveTickets,
            'urgentTickets' => $urgentTickets,
            'tickets' => $tickets,
        ]);
    }
}
