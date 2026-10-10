<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPageTest extends TestCase
{
    use RefreshDatabase;

    private function makePastEvent(array $attributes = []): Event
    {
        return Event::factory()->create($attributes + [
            'event_date' => now()->subDays(14)->toDateString(),
            'status' => Event::STATUS_PUBLISHED,
        ]);
    }

    private function makeCategory(Event $event): EventCategory
    {
        return EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Gran Fondo',
            'code' => 'GF',
            'distance_km' => 130,
            'elevation_gain_m' => 1450,
            'price' => 500000,
            'quota' => 10,
            'registered_count' => 1,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 7,
        ]);
    }

    public function test_riwayat_page_renders_for_guest(): void
    {
        $this->get('/riwayat')
            ->assertOk()
            ->assertSee('Riwayat Balap')
            ->assertSee('Race Selesai')
            ->assertSee('Masuk untuk melihat riwayat')
            ->assertDontSee('Event Diikuti');
    }

    public function test_riwayat_archive_shows_past_event(): void
    {
        $event = $this->makePastEvent(['title' => 'Race Lampau Nusantara']);

        $this->get('/riwayat')
            ->assertOk()
            ->assertSee('Race Lampau Nusantara')
            ->assertSee(e($event->location_name));
    }

    public function test_riwayat_archive_excludes_upcoming_and_draft_events(): void
    {
        Event::factory()->create([
            'title' => 'Race Mendatang Rahasia',
            'event_date' => now()->addDays(30)->toDateString(),
            'status' => Event::STATUS_PUBLISHED,
        ]);

        Event::factory()->create([
            'title' => 'Race Draft Tersembunyi',
            'event_date' => now()->subDays(5)->toDateString(),
            'status' => Event::STATUS_DRAFT,
        ]);

        $this->get('/riwayat')
            ->assertOk()
            ->assertDontSee('Race Mendatang Rahasia')
            ->assertDontSee('Race Draft Tersembunyi');
    }

    public function test_qualification_page_is_removed(): void
    {
        $this->get('/qualification')->assertNotFound();
    }

    public function test_nav_uses_riwayat_instead_of_qualification(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('/riwayat')
            ->assertDontSee('/qualification');
    }

    public function test_logged_in_peserta_sees_their_registrations(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $event = Event::factory()->create(['event_date' => now()->addDays(20)->toDateString()]);
        $category = $this->makeCategory($event);

        Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Peserta Riwayat',
            'identity_number' => '3308012408980002',
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Ibu Darurat',
            'emergency_contact_relation' => 'Ibu',
            'emergency_contact_phone' => '081987654321',
            'bib_number' => 'VLS-GF-0001',
            'status' => Registration::STATUS_PAID,
        ]);

        $this->actingAs($user)->get('/riwayat')
            ->assertOk()
            ->assertSee($event->title)
            ->assertSee('VLS-GF-0001')
            ->assertSee('LUNAS')
            ->assertSee('Event Diikuti');
    }
}
