<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class PortalDashboard extends Component
{
    public string $activeTab = 'overview'; // 'overview', 'tickets', 'events', 'profile', 'help'
    public string $ticketStatus = 'all';   // 'all', 'paid', 'pending', 'cancelled'
    public string $search = '';

    // Form fields profile
    public string $name = '';
    public string $phone = '';
    public string $gender = '';
    public string $blood_type = '';
    public string $birth_date = '';

    public bool $profileSaved = false;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user) {
            $this->name = (string) $user->name;
            $this->phone = (string) ($user->phone ?? '');
            $this->gender = (string) ($user->gender ?? '');
            $this->blood_type = (string) ($user->blood_type ?? '');
            $this->birth_date = $user->birth_date ? $user->birth_date->format('Y-m-d') : '';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'tickets', 'events', 'profile', 'help'], true)) {
            $this->activeTab = $tab;
            $this->profileSaved = false;
        }
    }

    public function filterTickets(string $status): void
    {
        if (in_array($status, ['all', 'paid', 'pending', 'cancelled'], true)) {
            $this->ticketStatus = $status;
        }
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:male,female',
            'blood_type' => 'nullable|string|max:5',
            'birth_date' => 'nullable|date',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'gender' => $this->gender ?: null,
            'blood_type' => $this->blood_type ?: null,
            'birth_date' => $this->birth_date ?: null,
        ]);

        $this->profileSaved = true;
    }

    public function render()
    {
        $user = auth()->user();
        $registrations = $user->registrations()
            ->with(['category.event', 'ticket'])
            ->latest()
            ->get();

        $filteredRegistrations = $registrations
            ->filter(function (Registration $reg) {
                $statusMatch = match ($this->ticketStatus) {
                    'paid' => $reg->isPaid(),
                    'pending' => $reg->status === Registration::STATUS_PENDING,
                    'cancelled' => $reg->status === Registration::STATUS_CANCELLED,
                    default => true,
                };

                if ($this->search !== '') {
                    $query = strtolower($this->search);
                    $eventTitle = strtolower($reg->category?->event?->title ?? '');
                    $bib = strtolower($reg->bib_number ?? '');
                    $catName = strtolower($reg->category?->name ?? '');

                    $searchMatch = str_contains($eventTitle, $query)
                        || str_contains($bib, $query)
                        || str_contains($catName, $query);

                    return $statusMatch && $searchMatch;
                }

                return $statusMatch;
            })
            ->values();

        $openEvents = Event::query()
            ->where('status', '!=', Event::STATUS_DRAFT)
            ->where('status', '!=', Event::STATUS_CANCELLED)
            ->with(['categories'])
            ->orderBy('event_date', 'asc')
            ->take(6)
            ->get();

        return view('livewire.portal-dashboard', [
            'registrations' => $registrations,
            'filteredRegistrations' => $filteredRegistrations,
            'statTotal' => $registrations->count(),
            'statPaid' => $registrations->filter(fn (Registration $reg) => $reg->isPaid())->count(),
            'statPending' => $registrations->where('status', Registration::STATUS_PENDING)->count(),
            'statCancelled' => $registrations->where('status', Registration::STATUS_CANCELLED)->count(),
            'statBibs' => $registrations->filter(fn (Registration $reg) => !empty($reg->bib_number))->count(),
            'countdown' => $this->countdown($registrations),
            'openEvents' => $openEvents,
        ]);
    }

    protected function countdown(Collection $registrations): ?array
    {
        $next = $registrations
            ->map(fn (Registration $registration): ?Event => $registration->category?->event)
            ->filter()
            ->unique('id')
            ->filter(fn (Event $event): bool => $event->event_date !== null
                && Carbon::parse($event->event_date)->gte(today()))
            ->sortBy('event_date')
            ->first();

        if (! $next instanceof Event) {
            return null;
        }

        return [
            'event' => $next,
            'days' => max(0, (int) today()->diffInDays(Carbon::parse($next->event_date), false)),
        ];
    }
}
