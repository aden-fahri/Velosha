<?php

namespace Tests\Feature;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class EventCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_automatically_from_title(): void
    {
        $user = User::factory()->create();

        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Velosha Gran Fondo Bandung 2027',
            'description' => 'Deskripsi rute.',
            'location_name' => 'Bandung, Jawa Barat',
            'event_date' => '2027-05-09',
            'start_time' => '05:45:00',
        ]);

        $this->assertSame('velosha-gran-fondo-bandung-2027', $event->slug);
    }

    public function test_duplicate_title_gets_unique_slug_suffix(): void
    {
        $user = User::factory()->create();

        $first = Event::factory()->for($user)->create(['title' => 'Bromo Summit Challenge']);
        $second = Event::factory()->for($user)->create(['title' => 'Bromo Summit Challenge']);

        $this->assertSame('bromo-summit-challenge', $first->slug);
        $this->assertSame('bromo-summit-challenge-2', $second->slug);
    }

    public function test_seeder_creates_six_published_events_with_categories(): void
    {
        $this->seed([DemoUserSeeder::class, EventSeeder::class]);

        $this->assertSame(6, Event::count());
        $this->assertSame(6, Event::published()->count());
        $this->assertSame(6, Event::published()->upcoming()->count());
        $this->assertGreaterThan(0, Event::first()->categories()->count());
        $this->assertNotNull(Event::first()->lowestPrice);
        $this->assertNotNull(Event::first()->minRemainingSlots);

        $this->seed(EventSeeder::class);
        $this->assertSame(6, Event::count());
    }

    public function test_scope_published_excludes_draft_events(): void
    {
        User::factory()->create();
        Event::factory()->count(3)->create();
        Event::factory()->draft()->count(2)->create();

        $this->assertSame(3, Event::published()->count());
        $this->assertSame(5, Event::count());
    }

    public function test_resource_query_scopes_events_by_owner(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);
        $other = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Event::factory()->count(2)->create(['user_id' => $owner->id]);
        Event::factory()->create(['user_id' => $other->id]);

        Auth::login($owner);
        $this->assertSame(2, EventResource::getEloquentQuery()->count());

        Auth::login($admin);
        $this->assertSame(3, EventResource::getEloquentQuery()->count());
    }

    public function test_admin_and_penyelenggara_can_open_event_panel_pages(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        $this->actingAs($admin)
            ->get('/admin/events')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/events/create')
            ->assertOk();

        $this->actingAs($penyelenggara)
            ->get('/admin/events')
            ->assertOk();
    }

    public function test_peserta_cannot_access_event_panel(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->actingAs($peserta)
            ->get('/admin/events')
            ->assertForbidden();
    }

    public function test_admin_can_create_event_with_category_through_panel_form(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin);

        Livewire::test(CreateEvent::class)
            ->fillForm([
                'title' => 'Finale Test Fondo 2027',
                'type' => Event::TYPE_GRAN_FONDO,
                'status' => Event::STATUS_PUBLISHED,
                'event_date' => '2027-06-06',
                'start_time' => '05:45',
                'location_name' => 'Jakarta, DKI Jakarta',
                'description' => 'Rute uji coba untuk automated test.',
                'categories' => [
                    [
                        'name' => 'Gran Fondo',
                        'code' => 'GF',
                        'distance_km' => 130,
                        'elevation_gain_m' => 1450,
                        'price' => 500000,
                        'quota' => 100,
                        'registered_count' => 0,
                        'wave_start_time' => 'Wave 1 - 05:45 WIB',
                        'cut_off_hours' => 7,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('slug', 'finale-test-fondo-2027')->first();

        $this->assertNotNull($event);
        $this->assertSame($admin->id, $event->user_id);
        $this->assertSame(1, $event->categories()->count());
        $this->assertSame('GF', $event->categories()->first()->code);
    }
}
