<?php

namespace App\Livewire;

use App\Models\Event;
use App\Support\EventCardPresenter;
use Livewire\Attributes\Url;
use Livewire\Component;

class RaceCards extends Component
{
    #[Url(as: 'type', history: true, except: 'all')]
    public string $category = 'all';

    public function filter(string $category): void
    {
        if (in_array($category, $this->categories(), true)) {
            $this->category = $category;
        }
    }

    public function render()
    {
        if (! in_array($this->category, $this->categories(), true)) {
            $this->category = 'all';
        }

        return view('livewire.race-cards', [
            'tabs' => $this->tabs(),
            'races' => $this->filteredRaces(),
        ]);
    }

    public function tabs(): array
    {
        return [
            ['key' => 'all', 'label' => 'Semua'],
            ['key' => 'gran-fondo', 'label' => 'Gran Fondo'],
            ['key' => 'medio-fondo', 'label' => 'Medio Fondo'],
            ['key' => 'fun-ride', 'label' => 'Fun Ride'],
            ['key' => 'climb', 'label' => 'KOM Climb'],
        ];
    }

    public function categories(): array
    {
        return array_column($this->tabs(), 'key');
    }

    protected function filteredRaces(): array
    {
        $query = Event::query()
            ->published()
            ->upcoming()
            ->with(['categories', 'photos'])
            ->orderBy('event_date');

        if ($this->category !== 'all') {
            $query->where('type', str_replace('-', '_', $this->category));
        }

        return $query
            ->limit(3)
            ->get()
            ->map(fn (Event $event): array => EventCardPresenter::card($event))
            ->values()
            ->all();
    }
}
