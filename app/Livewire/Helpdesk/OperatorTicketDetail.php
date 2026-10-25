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
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app', ['nav' => false])]
#[Title('Detail Tiket — Velosha Helpdesk')]
class OperatorTicketDetail extends Component
{
    #[Locked]
    public int $ticketId;

    #[Validate('required|string|min:2|max:5000', message: 'Pesan balasan minimal 2 karakter.')]
    public string $replyMessage = '';

    public string $solutionSummary = '';
    public bool $isReference = false;
    public ?int $assignOperatorId = null;

    public function mount(int $ticketId): void
    {
        $user = Auth::user();
        if (! $user || (! $user->isOperator() && ! $user->isAdmin())) {
            abort(403, 'Akses ditolak. Halaman khusus staf helpdesk dan admin.');
        }

        $ticket = HelpdeskTicket::find($ticketId);
        if (! $ticket) {
            abort(404, 'Tiket tidak ditemukan.');
        }

        $this->ticketId = $ticket->id;
        $this->solutionSummary = (string) ($ticket->solution_summary ?? '');
        $this->isReference = (bool) $ticket->is_reference;
        $this->assignOperatorId = $ticket->operator_id;
    }

    public function claimTicket(): void
    {
        $ticket = HelpdeskTicket::findOrFail($this->ticketId);

        try {
            $service = app(TicketService::class);
            $service->claimTicket($ticket, Auth::user());
            $this->assignOperatorId = Auth::id();
            session()->flash('success', 'Tiket berhasil diklaim dan sekarang ditugaskan kepada Anda.');
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            session()->flash('error', 'Anda tidak berwenang mengklaim tiket ini.');
        }
    }

    public function updateAssignment(): void
    {
        $ticket = HelpdeskTicket::findOrFail($this->ticketId);

        if (! $this->assignOperatorId) {
            session()->flash('error', 'Silakan pilih operator yang valid.');
            return;
        }

        $operator = User::find($this->assignOperatorId);
        if (! $operator) {
            session()->flash('error', 'Operator tidak ditemukan.');
            return;
        }

        try {
            $service = app(TicketService::class);
            $service->assignOperator($ticket, $operator, Auth::user());
            session()->flash('success', "Tiket berhasil ditugaskan kepada {$operator->name}.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function sendReply(): void
    {
        $this->validateOnly('replyMessage');

        $ticket = HelpdeskTicket::findOrFail($this->ticketId);

        try {
            $service = app(TicketService::class);
            $service->addReply($ticket, Auth::user(), trim($this->replyMessage));
            $this->replyMessage = '';
            session()->flash('success', 'Balasan berhasil dikirim.');
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function closeTicket(): void
    {
        $ticket = HelpdeskTicket::findOrFail($this->ticketId);

        try {
            $service = app(TicketService::class);
            $service->changeStatus(
                $ticket,
                TicketStatus::CLOSED,
                Auth::user(),
                trim($this->solutionSummary) ?: null
            );

            if ($this->isReference) {
                $ticket->refresh();
                $ticket->update(['is_reference' => true]);
            }

            session()->flash('success', 'Tiket berhasil ditutup (Closed).');
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reopenOrProgress(): void
    {
        $ticket = HelpdeskTicket::findOrFail($this->ticketId);

        try {
            $service = app(TicketService::class);
            $service->changeStatus($ticket, TicketStatus::IN_PROGRESS, Auth::user());
            session()->flash('success', 'Status tiket diubah menjadi In-Progress.');
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $ticket = HelpdeskTicket::with(['reporter', 'operator', 'responses.responder'])
            ->findOrFail($this->ticketId);

        $operators = User::whereIn('role', [User::ROLE_OPERATOR, User::ROLE_ADMIN])
            ->orderBy('name')
            ->get();

        return view('livewire.helpdesk.operator-ticket-detail', [
            'ticket' => $ticket,
            'operators' => $operators,
        ]);
    }
}
