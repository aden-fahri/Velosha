<?php

namespace App\Livewire;

use App\Models\Event;
use App\Support\EventCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
            'all' => 'Semua Kategori',
            'gran-fondo' => 'Gran Fondo (120KM+)',
            'medio-fondo' => 'Medio Fondo (60-90KM)',
            'fun-ride' => 'Fun Ride (25-40KM)',
            'climb' => 'KOM Climb',
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
        return cache()->remember('catalog_month_options', 60, function () {
            $options = [];

            foreach (Event::published()->upcoming()->distinct()->pluck('event_date') as $date) {
                $key = EventCardPresenter::monthKey($date);
                $options[$key] = EventCardPresenter::monthLabel($key);
            }

            ksort($options);

            return $options;
        });
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

        $currentPage = max(1, (int) $this->getPage());
        $total = $query->count();

        if ($currentPage === 1) {
            $items = $query->take(4)->get();
        } else {
            $offset = 4 + ($currentPage - 2) * 9;
            $items = $query->skip($offset)->take(9)->get();
        }

        $items->transform(
            fn (Event $event): array => EventCardPresenter::card($event)
        );

        return new class($items, $total, 9, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]) extends LengthAwarePaginator {
            public function lastPage(): int
            {
                return $this->total() <= 4 ? 1 : 1 + (int) ceil(($this->total() - 4) / 9);
            }

            public function hasMorePages(): bool
            {
                return $this->currentPage() < $this->lastPage();
            }
        };
    }
}
