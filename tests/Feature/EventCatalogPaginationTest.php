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
}
