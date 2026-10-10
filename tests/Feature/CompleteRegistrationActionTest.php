<?php

namespace Tests\Feature;

use App\Actions\CompleteRegistrationAction;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CompleteRegistrationActionTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategory(int $quota = 10, int $registered = 0): EventCategory
    {
        $event = Event::factory()->create();

        return EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Gran Fondo 130KM',
            'code' => 'GF',
            'distance_km' => 130,
            'elevation_gain_m' => 1450,
            'price' => 500000,
            'quota' => $quota,
            'registered_count' => $registered,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 7,
        ]);
    }

    private function makePendingRegistration(EventCategory $category): Registration
    {
        $user = User::factory()->create(['role' => 'peserta']);

        return Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Budi Santoso',
            'identity_number' => '3308012408980001',
            'birth_date' => '1995-05-15',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Dewi Lestari',
            'emergency_contact_relation' => 'Istri',
            'emergency_contact_phone' => '081234567890',
            'status' => RegistrationStatus::PENDING->value,
        ]);
    }

    public function test_it_completes_registration_atomically(): void
    {
        $category = $this->makeCategory(quota: 5, registered: 0);
        $registration = $this->makePendingRegistration($category);

        $action = new CompleteRegistrationAction();
        $completed = $action->execute($registration);

        $this->assertSame(RegistrationStatus::PAID->value, $completed->status);
        $this->assertNotNull($completed->bib_number);
        $this->assertStringStartsWith('VLS-GF-', $completed->bib_number);

        // Check category count incremented
        $category->refresh();
        $this->assertSame(1, $category->registered_count);

        // Check ticket created
        $this->assertDatabaseHas('tickets', [
            'registration_id' => $completed->id,
        ]);
    }

    public function test_it_is_idempotent_if_already_paid(): void
    {
        $category = $this->makeCategory(quota: 5, registered: 0);
        $registration = $this->makePendingRegistration($category);

        $action = new CompleteRegistrationAction();
        $first = $action->execute($registration);

        $category->refresh();
        $this->assertSame(1, $category->registered_count);

        // Execute again on already paid registration
        $second = $action->execute($first);
        $this->assertSame($first->bib_number, $second->bib_number);

        $category->refresh();
        $this->assertSame(1, $category->registered_count);
    }

    public function test_it_cancels_and_throws_if_quota_exhausted(): void
    {
        $category = $this->makeCategory(quota: 1, registered: 1);
        $registration = $this->makePendingRegistration($category);

        $action = new CompleteRegistrationAction();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Slot kategori sudah penuh');

        try {
            $action->execute($registration);
        } finally {
            $registration->refresh();
            $this->assertSame(RegistrationStatus::CANCELLED->value, $registration->status);
            $this->assertNull($registration->bib_number);
            $this->assertNull($registration->ticket);
        }
    }
}
