<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\QuotaPackage;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketBibAndCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function makeRegistration(User $user, EventCategory $category, array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Budi Pratama',
            'birth_date' => '1995-05-15',
            'identity_number' => '3201123456789001',
            'email' => $user->email,
            'phone' => '081234567890',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Siti Pratama',
            'emergency_contact_relation' => 'Keluarga',
            'emergency_contact_phone' => '081299998888',
            'status' => Registration::STATUS_PAID,
            'bib_number' => '0147',
        ], $overrides));
    }

    public function test_ticket_page_has_bib_print_option_and_no_email_button(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $event = Event::factory()->create(['title' => 'Bandung Highland Classic 2026']);
        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Gran Fondo',
            'code' => 'GF120',
            'distance_km' => 120,
            'elevation_gain_m' => 850,
            'price' => 250000,
            'quota' => 50,
            'registered_count' => 1,
            'wave_start_time' => 'Wave 1 - 05:30 WIB',
            'cut_off_hours' => 6,
        ]);

        $reg = $this->makeRegistration($user, $category, ['bib_number' => '0147']);

        Ticket::create([
            'registration_id' => $reg->id,
            'ticket_code' => 'TICKET-0147',
            'qr_code_data' => 'VELOSHA-0147-DATA',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('ticket.show', $reg));
        $response->assertOk();
        $response->assertSee('Cetak Nomor BIB');
        $response->assertDontSee('Kirim ke email');

        // Test BIB print page
        $bibResponse = $this->get(route('ticket.bib', $reg));
        $bibResponse->assertOk();
        $bibResponse->assertSee('0147');
        $bibResponse->assertSee('Budi Pratama');
        $bibResponse->assertSee('Gran Fondo');
        $bibResponse->assertSee('Bandung Highland Classic 2026');
    }

    public function test_multi_ticket_switcher_displayed_when_user_has_multiple_registrations(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $event1 = Event::factory()->create(['title' => 'Bandung Highland Classic']);
        $cat1 = EventCategory::create([
            'event_id' => $event1->id,
            'name' => 'Gran Fondo',
            'code' => 'GF',
            'distance_km' => 120,
            'elevation_gain_m' => 900,
            'price' => 300000,
            'quota' => 50,
            'registered_count' => 1,
            'wave_start_time' => '05:30 WIB',
            'cut_off_hours' => 6,
        ]);

        $event2 = Event::factory()->create(['title' => 'Borobudur Heritage Ride']);
        $cat2 = EventCategory::create([
            'event_id' => $event2->id,
            'name' => 'Medio Fondo',
            'code' => 'MF',
            'distance_km' => 70,
            'elevation_gain_m' => 450,
            'price' => 200000,
            'quota' => 50,
            'registered_count' => 1,
            'wave_start_time' => '06:00 WIB',
            'cut_off_hours' => 4,
        ]);

        $reg1 = $this->makeRegistration($user, $cat1, ['bib_number' => '0147']);
        $reg2 = $this->makeRegistration($user, $cat2, ['bib_number' => '0258']);

        $this->actingAs($user);

        $response = $this->get(route('ticket.show', $reg1));
        $response->assertOk();
        $response->assertSee('2 Tiket Terdaftar');
        $response->assertSee('Bandung Highland Classic');
        $response->assertSee('Borobudur Heritage Ride');
        $response->assertSee('Medio Fondo');
        $response->assertSee('0147');
        $response->assertSee('0258');
    }

    public function test_peserta_without_org_visiting_organizer_dashboard_redirects_to_participant_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->actingAs($user);

        $response = $this->get('/organisasi/dasbor');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_creating_organization_with_package_redirects_to_package_with_auto_pay(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->actingAs($user);

        $package = QuotaPackage::create([
            'name' => 'Pro Community',
            'events_count' => 3,
            'price' => 500000,
            'description' => 'Paket 3 event',
            'is_active' => true,
        ]);

        $response = $this->post(route('organizations.store'), [
            'name' => 'Surabaya Cycling Club',
            'description' => 'Komunitas gowes Jawa Timur',
            'package_id' => $package->id,
        ]);

        $response->assertRedirect(route('packages.index', [
            'auto_pay' => $package->id,
            'org' => 'surabaya-cycling-club',
        ]));
    }
}
