<?php

namespace Tests\Feature;

use App\Livewire\EventCatalog;
use App\Livewire\RaceCards;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function makeEvent(array $attributes = [], array $categories = []): Event
    {
        $event = Event::factory()->create($attributes);

        foreach ($categories as $category) {
            $event->categories()->create($category);
        }

        return $event;
    }

    public function test_home_shows_published_race_cards_from_database(): void
    {
        $this->makeEvent(['title' => 'Test Grand Fondo Nusantara']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Test Grand Fondo Nusantara');
    }

    public function test_home_hides_draft_events(): void
    {
        $this->makeEvent(['title' => 'Draft Secret Race', 'status' => Event::STATUS_DRAFT]);

        $this->get('/')->assertOk()->assertDontSee('Draft Secret Race');
    }

    public function test_race_cards_filters_by_type(): void
    {
        $this->makeEvent(['title' => 'Climb Only Event', 'type' => Event::TYPE_CLIMB]);
        $this->makeEvent(['title' => 'Fun Only Event', 'type' => Event::TYPE_FUN_RIDE]);

        Livewire::test(RaceCards::class)
            ->call('filter', 'climb')
            ->assertSee('Climb Only Event')
            ->assertDontSee('Fun Only Event');
    }

    public function test_event_catalog_filters_by_type(): void
    {
        $this->makeEvent(['title' => 'Gran Fondo Catalog', 'type' => Event::TYPE_GRAN_FONDO]);
        $this->makeEvent(['title' => 'Climb Catalog', 'type' => Event::TYPE_CLIMB]);

        Livewire::test(EventCatalog::class, ['type' => 'climb'])
            ->assertSee('Climb Catalog')
            ->assertDontSee('Gran Fondo Catalog');
    }

    public function test_event_catalog_searches_by_keyword(): void
    {
        $this->makeEvent(['title' => 'Borobudur Sunrise Ride', 'location_name' => 'Magelang']);
        $this->makeEvent(['title' => 'Bali Coastal Fondo', 'location_name' => 'Badung']);

        Livewire::test(EventCatalog::class, ['search' => 'Borobudur'])
            ->assertSee('Borobudur Sunrise Ride')
            ->assertDontSee('Bali Coastal Fondo');
    }

    public function test_event_catalog_sorts_by_price_ascending(): void
    {
        $cheap = $this->makeEvent(
            ['title' => 'Murah Fondo', 'type' => Event::TYPE_GRAN_FONDO],
            [['name' => 'GF', 'code' => 'GF', 'distance_km' => 100, 'elevation_gain_m' => 800, 'price' => 100000, 'quota' => 50, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 5]]
        );
        $pricey = $this->makeEvent(
            ['title' => 'Mahal Fondo', 'type' => Event::TYPE_GRAN_FONDO],
            [['name' => 'GF', 'code' => 'GF', 'distance_km' => 120, 'elevation_gain_m' => 1200, 'price' => 900000, 'quota' => 50, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 6]]
        );

        $html = Livewire::test(EventCatalog::class, ['sort' => 'price-asc'])->html();

        $this->assertLessThan(
            strpos($html, $pricey->title),
            strpos($html, $cheap->title),
            'Event dengan harga terendah harus tampil lebih dulu.'
        );
    }

    public function test_event_detail_shows_database_data(): void
    {
        $event = $this->makeEvent(
            ['title' => 'Detail Event Spesial', 'location_name' => 'Magelang, Jawa Tengah'],
            [['name' => 'Gran Fondo', 'code' => 'GF', 'distance_km' => 130, 'elevation_gain_m' => 1450, 'price' => 500000, 'quota' => 100, 'registered_count' => 25, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7]]
        );

        $response = $this->get('/events/'.$event->slug);

        $response->assertOk();
        $response->assertSee('Detail Event Spesial');
        $response->assertSee('Magelang, Jawa Tengah');
        $response->assertSee('Gran Fondo');
        $response->assertSee('Rp 500.000');
        $response->assertSee('Slot Tersisa 75');
    }

    public function test_draft_event_detail_returns_404(): void
    {
        $event = $this->makeEvent(['status' => Event::STATUS_DRAFT]);

        $this->get('/events/'.$event->slug)->assertNotFound();
    }

    public function test_unknown_event_slug_returns_404(): void
    {
        $this->get('/events/tidak-ada-event-ini')->assertNotFound();
    }

    public function test_events_index_renders_catalog_with_database_data(): void
    {
        $this->makeEvent(['title' => 'Index Catalog Event']);

        $this->get('/events')->assertOk()->assertSee('Index Catalog Event');
    }
}
