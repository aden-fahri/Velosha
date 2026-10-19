<?php

namespace Tests\Feature\Security;

use App\Livewire\Checkout;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LockedPropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_id_is_scoped(): void
    {
        $userA = User::factory()->create(['role' => 'peserta']);
        $userB = User::factory()->create(['role' => 'peserta']);
        $event = Event::factory()->create();
        $category = EventCategory::create([
            'event_id' => $event->id, 'name' => 'Gran Fondo', 'code' => 'GF',
            'distance_km' => 130, 'elevation_gain_m' => 1450, 'price' => 500000,
            'quota' => 10, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7,
        ]);
        $regB = Registration::create([
            'user_id' => $userB->id, 'event_category_id' => $category->id,
            'participant_name' => 'B', 'identity_number' => '123', 'birth_date' => '1998-08-24',
            'gender' => 'male', 'blood_type' => 'O', 'emergency_contact_name' => 'x',
            'emergency_contact_relation' => 'Istri / Suami', 'emergency_contact_phone' => '081',
            'medical_notes' => null, 'club' => null, 'status' => Registration::STATUS_PENDING,
        ]);

        $this->actingAs($userA);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $threw = false;
        try {
            $component->set('registrationId', $regB->id);
        } catch (\Throwable) {
            $threw = true;
        }
        $this->assertTrue($threw, 'registrationId harus terkunci');

        // registration() ter-scope: ID milik B tidak terbaca oleh A
        $this->actingAs($userA);
        $checkout = app(\App\Livewire\Checkout::class);
        $checkout->registrationId = $regB->id;
        $this->assertNull($checkout->registration());
    }
}