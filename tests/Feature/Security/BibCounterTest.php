<?php

namespace Tests\Feature\Security;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibCounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_bib_counter_increments(): void
    {
        $event = Event::factory()->create();
        $category = EventCategory::create([
            'event_id' => $event->id, 'name' => 'Gran Fondo', 'code' => 'GF',
            'distance_km' => 130, 'elevation_gain_m' => 1450, 'price' => 500000,
            'quota' => 10, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7,
        ]);

        $b1 = Registration::generateBibNumber($category);
        $b2 = Registration::generateBibNumber($category);

        $this->assertNotSame($b1, $b2);
        $this->assertMatchesRegularExpression('/^VLS-GF-\d{4}$/', $b1);
    }
}