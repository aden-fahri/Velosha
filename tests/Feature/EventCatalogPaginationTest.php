<?php

namespace Tests\Feature;

use App\Livewire\EventCatalog;
use App\Models\Event;
use App\Models\EventCategory;
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
        $response->assertSee('Race Nomor 4');
        $response->assertDontSee('Race Nomor 5');
        $response->assertDontSee('Race Nomor 10');
        $response->assertSee('Punya event sepeda?');
        $response->assertSee('gotoPage', false);
        $response->assertSee('aria-label="Halaman berikutnya"', false);
    }

    public function test_events_page_two_shows_tenth_event(): void
    {
        $this->createEvents(10);

        $response = $this->get('/events?page=2');

        $response->assertOk();
        $response->assertSee('Race Nomor 5');
        $response->assertSee('Race Nomor 10');
        $response->assertDontSee('Race Nomor 2');
        $response->assertDontSee('Race Nomor 4');
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('aria-label="Halaman sebelumnya"', false);
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

        $this->assertStringContainsString('events-pagination-nav', $html);
        $this->assertStringContainsString('page-btn', $html);
        $this->assertStringNotContainsString('bg-gray-100', $html);
        $this->assertStringNotContainsString('text-blue-', $html);
    }

    public function test_events_page_banner_uses_charcoal_header(): void
    {
        $this->get('/events')
            ->assertOk()
            ->assertSee('var(--butter', false);
    }

    public function test_twenty_five_events_paginate_without_duplicates_or_gaps(): void
    {
        $createdTitles = [];
        for ($i = 1; $i <= 25; $i++) {
            $title = sprintf('Distro Event %02d', $i);
            $createdTitles[] = $title;
            Event::factory()->create([
                'title' => $title,
                'event_date' => now()->addDays($i)->format('Y-m-d'),
            ]);
        }

        $seenTitles = [];
        $expectedCounts = [1 => 4, 2 => 9, 3 => 9, 4 => 3];

        foreach ([1, 2, 3, 4] as $page) {
            $response = $this->get('/events?page='.$page);
            $response->assertOk();

            $pageTitles = [];
            foreach ($createdTitles as $title) {
                if (str_contains($response->getContent(), $title)) {
                    $pageTitles[] = $title;
                }
            }

            $this->assertCount($expectedCounts[$page], $pageTitles, "Page {$page} must show exactly {$expectedCounts[$page]} events");

            foreach ($pageTitles as $title) {
                $this->assertNotContains($title, $seenTitles, "Event {$title} appeared more than once across pagination");
                $seenTitles[] = $title;
            }
        }

        $this->assertCount(25, $seenTitles);
        $this->assertEqualsCanonicalizing($createdTitles, $seenTitles);
    }

    public function test_seven_events_with_same_date_paginate_consistently(): void
    {
        $sameDate = now()->addDays(10)->format('Y-m-d');
        $createdTitles = [];
        for ($i = 1; $i <= 7; $i++) {
            $title = sprintf('SameDate Race %02d', $i);
            $createdTitles[] = $title;
            Event::factory()->create([
                'title' => $title,
                'event_date' => $sameDate,
            ]);
        }

        $p1 = $this->get('/events?page=1');
        $p2 = $this->get('/events?page=2');

        $p1Titles = [];
        $p2Titles = [];
        foreach ($createdTitles as $title) {
            if (str_contains($p1->getContent(), $title)) {
                $p1Titles[] = $title;
            }
            if (str_contains($p2->getContent(), $title)) {
                $p2Titles[] = $title;
            }
        }

        $this->assertCount(4, $p1Titles, 'Page 1 must show 4 events');
        $this->assertCount(3, $p2Titles, 'Page 2 must show 3 events');

        $overlap = array_intersect($p1Titles, $p2Titles);
        $this->assertEmpty($overlap, 'No duplicate events should appear between page 1 and page 2');
    }

    public function test_default_sort_paginates_deterministically_with_tied_dates(): void
    {
        $tiedDate = now()->addDays(20)->format('Y-m-d');
        $this->assertSortModePaginationDeterminism('default', function (int $i) use ($tiedDate): Event {
            return Event::factory()->create([
                'title' => sprintf('Tied Date %02d', $i),
                'event_date' => $tiedDate,
                'status' => Event::STATUS_PUBLISHED,
            ]);
        });
    }

    public function test_popular_sort_paginates_deterministically_with_tied_registrations(): void
    {
        $tiedDate = now()->addDays(20)->format('Y-m-d');
        $this->assertSortModePaginationDeterminism('popular', function (int $i) use ($tiedDate): Event {
            $event = Event::factory()->create([
                'title' => sprintf('Tied Popular %02d', $i),
                'event_date' => $tiedDate,
                'status' => Event::STATUS_PUBLISHED,
            ]);
            EventCategory::create([
                'event_id' => $event->id,
                'name' => 'Category A',
                'code' => 'POP-'.$i,
                'distance_km' => 100,
                'elevation_gain_m' => 800,
                'price' => 100000,
                'quota' => 200,
                'registered_count' => 75,
                'wave_start_time' => '06:00:00',
                'cut_off_hours' => 5,
            ]);
            return $event;
        });
    }

    public function test_price_asc_sort_paginates_deterministically_with_tied_prices(): void
    {
        $tiedDate = now()->addDays(20)->format('Y-m-d');
        $this->assertSortModePaginationDeterminism('price-asc', function (int $i) use ($tiedDate): Event {
            $event = Event::factory()->create([
                'title' => sprintf('Tied Price %02d', $i),
                'event_date' => $tiedDate,
                'status' => Event::STATUS_PUBLISHED,
            ]);
            EventCategory::create([
                'event_id' => $event->id,
                'name' => 'Category A',
                'code' => 'PRC-'.$i,
                'distance_km' => 100,
                'elevation_gain_m' => 800,
                'price' => 250000,
                'quota' => 200,
                'registered_count' => 10,
                'wave_start_time' => '06:00:00',
                'cut_off_hours' => 5,
            ]);
            return $event;
        });
    }

    public function test_distance_desc_sort_paginates_deterministically_with_tied_distances(): void
    {
        $tiedDate = now()->addDays(20)->format('Y-m-d');
        $this->assertSortModePaginationDeterminism('distance-desc', function (int $i) use ($tiedDate): Event {
            $event = Event::factory()->create([
                'title' => sprintf('Tied Distance %02d', $i),
                'event_date' => $tiedDate,
                'status' => Event::STATUS_PUBLISHED,
            ]);
            EventCategory::create([
                'event_id' => $event->id,
                'name' => 'Category A',
                'code' => 'DST-'.$i,
                'distance_km' => 125.5,
                'elevation_gain_m' => 800,
                'price' => 200000,
                'quota' => 200,
                'registered_count' => 10,
                'wave_start_time' => '06:00:00',
                'cut_off_hours' => 5,
            ]);
            return $event;
        });
    }

    public function test_elevation_desc_sort_paginates_deterministically_with_tied_elevations(): void
    {
        $tiedDate = now()->addDays(20)->format('Y-m-d');
        $this->assertSortModePaginationDeterminism('elevation-desc', function (int $i) use ($tiedDate): Event {
            $event = Event::factory()->create([
                'title' => sprintf('Tied Elevation %02d', $i),
                'event_date' => $tiedDate,
                'status' => Event::STATUS_PUBLISHED,
            ]);
            EventCategory::create([
                'event_id' => $event->id,
                'name' => 'Category A',
                'code' => 'ELV-'.$i,
                'distance_km' => 100,
                'elevation_gain_m' => 1650.0,
                'price' => 200000,
                'quota' => 200,
                'registered_count' => 10,
                'wave_start_time' => '06:00:00',
                'cut_off_hours' => 5,
            ]);
            return $event;
        });
    }

    protected function assertSortModePaginationDeterminism(string $sortMode, callable $createEventCallback): void
    {
        $createdEvents = [];
        $slugToId = [];
        for ($i = 1; $i <= 25; $i++) {
            $event = $createEventCallback($i);
            $createdEvents[] = $event;
            $slugToId[$event->slug] = $event->id;
        }

        $allSeenIds = [];
        $expectedSizes = [1 => 4, 2 => 9, 3 => 9, 4 => 3];

        foreach ([1, 2, 3, 4] as $page) {
            $url = $sortMode === 'default' ? '/events?page='.$page : '/events?sort='.$sortMode.'&page='.$page;

            $response1 = $this->get($url);
            $response1->assertOk();
            $pageIds1 = $this->extractEventIdsFromHtml($response1->getContent(), $slugToId);

            $this->assertCount(
                $expectedSizes[$page],
                $pageIds1,
                "Page {$page} in sort mode '{$sortMode}' must show exactly {$expectedSizes[$page]} event cards"
            );

            $response2 = $this->get($url);
            $response2->assertOk();
            $pageIds2 = $this->extractEventIdsFromHtml($response2->getContent(), $slugToId);

            $this->assertSame(
                $pageIds1,
                $pageIds2,
                "Consecutive requests to page {$page} in sort mode '{$sortMode}' must return identical ordering"
            );

            foreach ($pageIds1 as $id) {
                $this->assertNotContains(
                    $id,
                    $allSeenIds,
                    "Event ID {$id} appeared more than once across pagination pages"
                );
                $allSeenIds[] = $id;
            }
        }

        $this->assertCount(25, $allSeenIds);
        $expectedIds = array_values($slugToId);
        $this->assertEqualsCanonicalizing(
            $expectedIds,
            $allSeenIds,
            "The union of event IDs across pages 1 to 4 must contain all 25 event IDs without gaps or duplicates"
        );
    }

    protected function extractEventIdsFromHtml(string $html, array $slugToId): array
    {
        preg_match_all('/wire:key="event-([^"]+)"/', $html, $matches);
        $ids = [];
        foreach ($matches[1] as $slug) {
            if (isset($slugToId[$slug])) {
                $ids[] = $slugToId[$slug];
            }
        }
        return $ids;
    }
}
