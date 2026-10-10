<?php

namespace App\Livewire;

use App\Models\Event;
use App\Support\EventCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EventCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'type', except: 'all')]
    public string $type = 'all';

    #[Url(as: 'month', except: '')]
    public string $month = '';

    #[Url(as: 'sort', except: 'date-asc')]
    public string $sort = 'date-asc';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'month', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->type = 'all';
        $this->month = '';
        $this->sort = 'date-asc';
        $this->resetPage();
    }

    public function clearFilter(string $key): void
    {
        match ($key) {
            'search' => $this->search = '',
            'type' => $this->type = 'all',
            'month' => $this->month = '',
            default => $this->sort = 'date-asc',
        };
        $this->resetPage();
    }

    public function render()
    {
        $this->normalize();

        return view('livewire.event-catalog', [
            'cards' => $this->cards(),
            'typeOptions' => $this->typeOptions(),
            'monthOptions' => $this->monthOptions(),
            'activeFilters' => $this->activeFilters(),
        ]);
    }

    protected function normalize(): void
    {
        if (! array_key_exists($this->type, $this->typeOptions())) {
            $this->type = 'all';
        }

        if (! in_array($this->sort, $this->sortOptions(), true)) {
            $this->sort = 'date-asc';
        }

        if ($this->month !== '' && ! array_key_exists($this->month, $this->monthOptions())) {
            $this->month = '';
        }

        $this->search = trim($this->search);
    }

    protected function typeOptions(): array
    {
        return [
            'all' => 'Semua Kategori Jarak',
            'gran-fondo' => 'Gran Fondo (120KM+)',
            'medio-fondo' => 'Medio Fondo (60-90KM)',
            'fun-ride' => 'Fun Ride & Heritage (25-40KM)',
            'climb' => 'King of Mountain (KOM Climb)',
        ];
    }

    protected function sortOptions(): array
    {
        return [
            'date-asc',
            'popular',
            'distance-desc',
            'elevation-desc',
            'price-asc',
        ];
    }

    protected function monthOptions(): array
    {
        $options = [];

        foreach (Event::published()->upcoming()->distinct()->pluck('event_date') as $date) {
            $key = EventCardPresenter::monthKey($date);
            $options[$key] = EventCardPresenter::monthLabel($key);
        }

        ksort($options);

        return $options;
    }

    protected function activeFilters(): array
    {
        $filters = [];

        if ($this->search !== '') {
            $filters[] = ['key' => 'search', 'label' => 'Kata kunci: '.$this->search];
        }

        if ($this->type !== 'all') {
            $filters[] = ['key' => 'type', 'label' => 'Kategori: '.trim(preg_replace('/\(.*\)/', '', $this->typeOptions()[$this->type]))];
        }

        if ($this->month !== '') {
            $filters[] = ['key' => 'month', 'label' => 'Bulan: '.EventCardPresenter::monthLabel($this->month)];
        }

        if ($this->sort !== 'date-asc') {
            $filters[] = ['key' => 'sort', 'label' => 'Urutan: '.$this->sortLabels()[$this->sort]];
        }

        return $filters;
    }

    protected function sortLabels(): array
    {
        return [
            'popular' => 'Paling Populer',
            'distance-desc' => 'Jarak terjauh',
            'elevation-desc' => 'Elevasi tertinggi',
            'price-asc' => 'Biaya terendah',
        ];
    }

    protected function cards()
    {
        $query = Event::query()
            ->published()
            ->upcoming()
            ->with(['categories', 'photos']);

        if ($this->type !== 'all') {
            $query->where('type', str_replace('-', '_', $this->type));
        }

        if ($this->month !== '' && preg_match('/^\d{4}-\d{2}$/', $this->month)) {
            [$year, $month] = explode('-', $this->month);
            $query->whereYear('event_date', $year)->whereMonth('event_date', $month);
        }

        if ($this->search !== '') {
            $term = $this->search;
            $query->where(fn (Builder $builder): Builder => $builder
                ->where('title', 'like', "%{$term}%")
                ->orWhere('location_name', 'like', "%{$term}%"));
        }

        match ($this->sort) {
            'popular' => $query
                ->withSum('categories as total_registered', 'registered_count')
                ->orderByDesc('total_registered'),
            'price-asc' => $query
                ->withMin('categories as lowest_price', 'price')
                ->orderBy('lowest_price'),
            'distance-desc' => $query
                ->withMax('categories as max_distance', 'distance_km')
                ->orderByDesc('max_distance'),
            'elevation-desc' => $query
                ->withMax('categories as max_elevation', 'elevation_gain_m')
                ->orderByDesc('max_elevation'),
            default => $query->orderBy('event_date'),
        };

        $paginator = $query->paginate(9);
        $paginator->getCollection()->transform(
            fn (Event $event): array => EventCardPresenter::card($event)
        );

        return $paginator;
    }
}
