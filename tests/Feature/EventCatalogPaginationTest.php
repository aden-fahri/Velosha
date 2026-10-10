<?php

namespace Tests\Feature;

use App\Livewire\EventCatalog;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventCatalogPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function createEvents(int $count): void
    {
        foreach (range(1, $count) as $i) {
            Event::factory()->create([
                'title' => 'Race Nomor '.$i,
                'event_date' => now()->addDays($i)->format('Y-m-d'),
            ]);
        }
    }

    public function test_events_page_shows_only_nine_cards_with_pagination_links(): void
    {
        $this->createEvents(10);

        $response = $this->get('/events');

        $response->assertOk();
        $response->assertSee('Race Nomor 1');
        $response->assertSee('Race Nomor 9');
        $response->assertDontSee('Race Nomor 10');
        $response->assertSee('hasil filter');
        $response->assertSee('gotoPage', false);
        $response->assertSee('Sebelumnya');
    }

    public function test_events_page_two_shows_tenth_event(): void
    {
        $this->createEvents(10);

        $response = $this->get('/events?page=2');

        $response->assertOk();
        $response->assertSee('Race Nomor 10');
        $response->assertDontSee('Race Nomor 5');
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('Berikutnya');
    }

    public function test_changing_filter_resets_pagination_to_first_page(): void
    {
        $this->createEvents(10);

        Livewire::test(EventCatalog::class)
            ->call('gotoPage', 2)
            ->assertSet('paginators.page', 2)
            ->set('search', 'Race')
            ->assertSet('paginators.page', 1);
    }

    public function test_pagination_view_uses_site_palette_not_default_gray(): void
    {
        $this->createEvents(10);

        $html = $this->get('/events')->getContent();

        $this->assertStringContainsString('veloborder', $html);
        $this->assertStringNotContainsString('bg-gray-100', $html);
        $this->assertStringNotContainsString('text-blue-', $html);
    }

    public function test_events_page_banner_uses_charcoal_header(): void
    {
        $this->get('/events')
            ->assertOk()
            ->assertSee('bg-[#2E343B]', false);
    }
}
