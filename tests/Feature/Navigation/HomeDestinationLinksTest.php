<?php

namespace Tests\Feature\Navigation;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeDestinationLinksTest extends TestCase
{
    use RefreshDatabase;

    private function createPaidRegistration(User $user): Registration
    {
        $event = Event::factory()->create();
        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Gran Fondo',
            'code' => 'GF',
            'distance_km' => 120,
            'elevation_gain_m' => 1200,
            'price' => 500000,
            'quota' => 100,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 7,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => $user->name,
            'identity_number' => '3201' . fake()->unique()->numerify('############'),
            'birth_date' => '1995-05-15',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_relation' => 'Family',
            'emergency_contact_phone' => '08123456789',
            'status' => Registration::STATUS_PAID,
            'bib_number' => 'GF-' . fake()->unique()->numerify('#####'),
        ]);

        Ticket::issueFor($registration);

        return $registration;
    }

    public function test_navbar_destination_link_by_role(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        // 1. Peserta sees "Portal Saya" -> /dashboard
        $this->actingAs($peserta)
            ->get('/')
            ->assertOk()
            ->assertSee('Portal Saya')
            ->assertSee('/dashboard');

        // 2. Operator sees "Antrian Helpdesk" -> /helpdesk (NOT /dashboard!)
        $this->actingAs($operator)
            ->get('/')
            ->assertOk()
            ->assertSee('Antrian Helpdesk')
            ->assertSee('/helpdesk')
            ->assertDontSee('Portal Saya');

        // 3. Admin sees "Panel Admin" -> /admin
        $this->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertSee('Panel Admin')
            ->assertSee('/admin')
            ->assertDontSee('Portal Saya');

        // 4. Penyelenggara sees "Panel Admin" -> /admin
        $this->actingAs($penyelenggara)
            ->get('/')
            ->assertOk()
            ->assertSee('Panel Admin')
            ->assertSee('/admin')
            ->assertDontSee('Portal Saya');
    }

    public function test_403_page_destination_link_by_role(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        // 1. Peserta
        $this->actingAs($peserta);
        $resPeserta = $this->view('errors.403');
        $resPeserta->assertSee('Ke Dashboard Saya');
        $resPeserta->assertSee('/dashboard');

        // 2. Operator
        $this->actingAs($operator);
        $resOperator = $this->view('errors.403');
        $resOperator->assertSee('Antrian Helpdesk');
        $resOperator->assertSee('/helpdesk');
        $resOperator->assertDontSee('Ke Dashboard Saya');

        // 3. Admin
        $this->actingAs($admin);
        $resAdmin = $this->view('errors.403');
        $resAdmin->assertSee('Ke Panel Admin');
        $resAdmin->assertSee('/admin');

        // 4. Penyelenggara
        $this->actingAs($penyelenggara);
        $resPenyelenggara = $this->view('errors.403');
        $resPenyelenggara->assertSee('Ke Panel Admin');
        $resPenyelenggara->assertSee('/admin');
    }

    public function test_ticket_page_destination_link_by_role(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        $regPeserta = $this->createPaidRegistration($peserta);
        $regOperator = $this->createPaidRegistration($operator);
        $regAdmin = $this->createPaidRegistration($admin);
        $regPenyelenggara = $this->createPaidRegistration($penyelenggara);

        // 1. Peserta sees "Buka Dashboard Peserta" -> /dashboard
        $this->actingAs($peserta)
            ->get('/ticket/' . $regPeserta->id)
            ->assertOk()
            ->assertSee('Buka Dashboard Peserta')
            ->assertSee('/dashboard');

        // 2. Operator sees "Antrian Helpdesk" -> /helpdesk (NOT /dashboard!)
        $this->actingAs($operator)
            ->get('/ticket/' . $regOperator->id)
            ->assertOk()
            ->assertSee('Antrian Helpdesk')
            ->assertSee('/helpdesk')
            ->assertDontSee('Buka Dashboard Peserta');

        // 3. Admin sees "Kembali ke Panel Pengelola" -> /admin
        $this->actingAs($admin)
            ->get('/ticket/' . $regAdmin->id)
            ->assertOk()
            ->assertSee('Kembali ke Panel Pengelola')
            ->assertSee('/admin');

        // 4. Penyelenggara sees "Kembali ke Panel Pengelola" -> /admin
        $this->actingAs($penyelenggara)
            ->get('/ticket/' . $regPenyelenggara->id)
            ->assertOk()
            ->assertSee('Kembali ke Panel Pengelola')
            ->assertSee('/admin');
    }
}
