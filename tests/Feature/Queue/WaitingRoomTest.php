<?php

namespace Tests\Feature\Queue;

use App\Livewire\WaitingRoom;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WaitingRoomTest extends TestCase
{
    use RefreshDatabase;

    protected function createEventWithCategory(int $quota = 2, int $registered = 0): array
    {
        $organizer = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'title' => 'Bromo KOM Challenge',
            'slug' => 'bromo-kom-challenge',
            'type' => Event::TYPE_CLIMB,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => 'KOM Climb',
            'code' => 'CLIMB',
            'distance_km' => 45,
            'elevation_gain_m' => 2000,
            'price' => 350000,
            'quota' => $quota,
            'registered_count' => $registered,
            'wave_start_time' => 'Wave 1 - 06:00 WIB',
            'cut_off_hours' => 5,
        ]);

        return [$event, $category];
    }

    public function test_user_is_instantly_granted_slot_and_redirected_to_checkout_when_free(): void
    {
        [$event, $category] = $this->createEventWithCategory(quota: 5, registered: 0);
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->actingAs($user);

        Livewire::test(WaitingRoom::class, ['eventSlug' => $event->slug, 'categoryId' => $category->id])
            ->assertRedirect(route('checkout', ['event' => $event->slug, 'category' => $category->id]));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_HELD,
        ]);
    }

    public function test_user_enters_waiting_queue_when_all_slots_are_held_by_others(): void
    {
        [$event, $category] = $this->createEventWithCategory(quota: 1, registered: 0);

        $userA = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $userB = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // User A memegang satu-satunya slot yang ada
        Reservation::create([
            'user_id' => $userA->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_HELD,
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->actingAs($userB);

        Livewire::test(WaitingRoom::class, ['eventSlug' => $event->slug, 'categoryId' => $category->id])
            ->assertSet('queuePosition', 1)
            ->assertSet('slotSecured', false)
            ->assertSee('Posisi Antrean Anda')
            ->assertSee('#1');

        $this->assertDatabaseHas('reservations', [
            'user_id' => $userB->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_WAITING,
        ]);
    }

    public function test_waiting_user_is_promoted_to_held_slot_when_earlier_reservation_expires(): void
    {
        [$event, $category] = $this->createEventWithCategory(quota: 1, registered: 0);

        $userA = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $userB = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // User A memegang slot tapi sudah expired
        Reservation::create([
            'user_id' => $userA->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_HELD,
            'expires_at' => now()->subMinutes(2),
        ]);

        // User B sedang menunggu antrean
        Reservation::create([
            'user_id' => $userB->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_WAITING,
        ]);

        $this->actingAs($userB);

        // Saat polling jalan, slot User A dilepas dan User B langsung dipromosikan ke checkout
        Livewire::test(WaitingRoom::class, ['eventSlug' => $event->slug, 'categoryId' => $category->id])
            ->assertRedirect(route('checkout', ['event' => $event->slug, 'category' => $category->id]));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $userA->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_RELEASED,
        ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $userB->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_HELD,
        ]);
    }

    public function test_user_can_leave_queue(): void
    {
        [$event, $category] = $this->createEventWithCategory(quota: 1, registered: 0);
        $userA = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $userB = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Slot penuh di-hold User A
        Reservation::create([
            'user_id' => $userA->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_HELD,
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->actingAs($userB);

        Livewire::test(WaitingRoom::class, ['eventSlug' => $event->slug, 'categoryId' => $category->id])
            ->assertSet('queuePosition', 1)
            ->call('leaveQueue')
            ->assertRedirect(route('events.show', ['slug' => $event->slug]));

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $userB->id,
            'event_category_id' => $category->id,
            'status' => Reservation::STATUS_WAITING,
        ]);
    }

    public function test_waiting_room_shows_sold_out_when_quota_is_exhausted(): void
    {
        [$event, $category] = $this->createEventWithCategory(quota: 5, registered: 5);
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->actingAs($user);

        Livewire::test(WaitingRoom::class, ['eventSlug' => $event->slug, 'categoryId' => $category->id])
            ->assertSet('isSoldOut', true)
            ->assertSee('Tiket Terjual Habis');
    }
}

