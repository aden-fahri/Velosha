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

class PaymentGuardTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(): Event
    {
        return Event::factory()->create();
    }

    private function makeCategory(Event $event, array $attributes = []): EventCategory
    {
        return EventCategory::create($attributes + [
            'event_id' => $event->id,
            'name' => 'Gran Fondo',
            'code' => 'GF',
            'distance_km' => 130,
            'elevation_gain_m' => 1450,
            'price' => 500000,
            'quota' => 10,
            'registered_count' => 0,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 7,
        ]);
    }

    private function pendingRegistration(User $user, EventCategory $category): Registration
    {
        return Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Arif Rahman Wibowo',
            'identity_number' => '3308012408980002',
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Siti Handayani',
            'emergency_contact_relation' => 'Istri / Suami',
            'emergency_contact_phone' => '081987654321',
            'medical_notes' => null,
            'club' => 'Menoreh Peloton CC',
            'status' => Registration::STATUS_PENDING,
        ]);
    }

    public function test_simulate_paid_rejected_when_flag_off(): void
    {
        config(['payments.simulation' => false]);

        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $this->pendingRegistration($user, $category);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->call('simulatePaid');
        $registration = Registration::first();
        $this->assertSame(Registration::STATUS_PENDING, $registration->status);

        $registration = Registration::first();
        $this->assertSame(Registration::STATUS_PENDING, $registration->status);
    }

    public function test_payment_modal_hidden_when_flag_off(): void
    {
        config(['payments.simulation' => false]);

        $user = User::factory()->create(['role' => 'peserta']);
        $event = Event::factory()->create();
        $category = EventCategory::create([
            'event_id' => $event->id, 'name' => 'Gran Fondo', 'code' => 'GF',
            'distance_km' => 130, 'elevation_gain_m' => 1450, 'price' => 500000,
            'quota' => 10, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7,
        ]);
        Registration::create([
            'user_id' => $user->id, 'event_category_id' => $category->id,
            'participant_name' => 'Arif', 'identity_number' => '123', 'birth_date' => '1998-08-24',
            'gender' => 'male', 'blood_type' => 'O', 'emergency_contact_name' => 'x',
            'emergency_contact_relation' => 'Istri / Suami', 'emergency_contact_phone' => '081',
            'medical_notes' => null, 'club' => null, 'status' => Registration::STATUS_PENDING,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->call('openPayment');
        $component->assertDontSee('MIDTRANS SNAP');
    }
}