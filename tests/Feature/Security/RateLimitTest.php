<?php

namespace Tests\Feature\Security;

use App\Livewire\Checkout;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rate_limit_message(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = Event::factory()->create();
        $category = EventCategory::create([
            'event_id' => $event->id, 'name' => 'Gran Fondo', 'code' => 'GF',
            'distance_km' => 130, 'elevation_gain_m' => 1450, 'price' => 500000,
            'quota' => 10, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->set([
            'participant_name' => 'Arif Rahman Wibowo',
            'identity_number' => '3308012408980002',
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'email' => $user->email,
            'phone' => '081223344556',
            'club' => 'Menoreh Peloton CC',
            'emergency_contact_name' => 'Siti Handayani',
            'emergency_contact_relation' => 'Istri / Suami',
            'emergency_contact_phone' => '081987654321',
            'medical_notes' => 'Tidak ada riwayat asma',
        ]);

        for ($i = 0; $i < 11; $i++) {
            $component->call('toStep3');
        }

        $component->assertSee('Terlalu banyak permintaan');
    }
}