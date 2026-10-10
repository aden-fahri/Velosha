<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class PortalDashboard extends Component
{
    public string $tab = 'all';

    public function filter(string $tab): void
    {
        if (in_array($tab, $this->tabs(), true)) {
            $this->tab = $tab;
        }
    }

    public function render()
    {
        if (! in_array($this->tab, $this->tabs(), true)) {
            $this->tab = 'all';
        }

        $registrations = auth()->user()->registrations()
            ->with(['category.event', 'ticket'])
            ->latest()
            ->get();

        return view('livewire.portal-dashboard', [
            'registrations' => $registrations,
            'filtered' => $this->filterRegistrations($registrations),
            'statTotal' => $registrations->count(),
            'statPaid' => $registrations->filter(fn (Registration $registration): bool => $registration->isPaid())->count(),
            'statPending' => $registrations->where('status', Registration::STATUS_PENDING)->count(),
            'statCancelled' => $registrations->where('status', Registration::STATUS_CANCELLED)->count(),
            'countdown' => $this->countdown($registrations),
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function tabs(): array
    {
        return ['all', 'paid', 'pending', 'cancelled'];
    }

    /**
     * @param  Collection<int, Registration>  $registrations
     * @return Collection<int, Registration>
     */
    protected function filterRegistrations(Collection $registrations): Collection
    {
        return $registrations
            ->filter(fn (Registration $registration): bool => match ($this->tab) {
                'paid' => $registration->isPaid(),
                'pending' => $registration->status === Registration::STATUS_PENDING,
                'cancelled' => $registration->status === Registration::STATUS_CANCELLED,
                default => true,
            })
            ->values();
    }

    /**
     * Hitung mundur ke event terdekat di antara pendaftaran milik pengguna.
     *
     * @param  Collection<int, Registration>  $registrations
     * @return array{event: Event, days: int}|null
     */
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
