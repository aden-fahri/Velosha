<?php

namespace Tests\Feature\Security;

use App\Actions\CompleteRegistrationAction;
use App\Livewire\Checkout;
use App\Livewire\Helpdesk\MyTickets;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\HelpdeskTicket;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IdorSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createParticipant(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => User::ROLE_PESERTA,
        ], $attributes));
    }

    private function createPenyelenggara(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => User::ROLE_PENYELENGGARA,
        ], $attributes));
    }

    private function createEventWithRegistration(User $user, ?User $penyelenggara = null): array
    {
        $penyelenggara = $penyelenggara ?: $this->createPenyelenggara();
        $event = Event::factory()->create(['user_id' => $penyelenggara->id]);

        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Gran Fondo Test',
            'code' => 'GFT',
            'distance_km' => 130,
            'elevation_gain_m' => 1450,
            'price' => 500000,
            'quota' => 10,
            'registered_count' => 1,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 7,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Peserta B Audit',
            'identity_number' => '3308012408980002',
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Kontak Darurat B',
            'emergency_contact_relation' => 'Keluarga',
            'emergency_contact_phone' => '081987654321',
            'medical_notes' => null,
            'club' => 'Velosha Test Club',
            'status' => Registration::STATUS_PAID,
            'bib_number' => 'VLS-GFT-0001',
        ]);

        $ticket = Ticket::issueFor($registration);

        return [$event, $category, $registration, $ticket, $penyelenggara];
    }

    // =========================================================================
    // TARGET A: GET /ticket/{registration} (E-Ticket Display)
    // =========================================================================

    /**
     * Target A (Positive Control): Owner can view own ticket with 200 OK.
     */
    public function test_target_a_positive_control_owner_can_view_own_ticket(): void
    {
        $owner = $this->createParticipant();
        [, , $registration] = $this->createEventWithRegistration($owner);

        $response = $this->actingAs($owner)->get('/ticket/' . $registration->id);

        $response->assertOk();
        $response->assertSee($registration->bib_number);
    }

    /**
     * Target A (Cross-User): Non-owner participant receives 403 Forbidden.
     */
    public function test_target_a_cross_user_other_participant_cannot_view_foreign_ticket(): void
    {
        $owner = $this->createParticipant();
        $attacker = $this->createParticipant();
        [, , $registration] = $this->createEventWithRegistration($owner);

        $response = $this->actingAs($attacker)->get('/ticket/' . $registration->id);

        $response->assertForbidden();
    }

    /**
     * Target A (Guest Control): Unauthenticated visitor redirected to /login (302).
     */
    public function test_target_a_guest_control_unauthenticated_visitor_redirected(): void
    {
        $owner = $this->createParticipant();
        [, , $registration] = $this->createEventWithRegistration($owner);

        $response = $this->get('/ticket/' . $registration->id);

        $response->assertRedirect('/login');
    }

    // =========================================================================
    // TARGET B: Livewire Checkout Property Tampering (registrationId)
    // =========================================================================

    /**
     * Target B (Positive Control): Owner mounts Checkout with category and valid registration.
     */
    public function test_target_b_positive_control_owner_mounts_checkout(): void
    {
        $owner = $this->createParticipant();
        [$event, $category] = $this->createEventWithRegistration($owner);

        $component = Livewire::actingAs($owner)->test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->assertOk();
        $this->assertSame($event->slug, $component->get('eventSlug'));
    }

    /**
     * Target B (Cross-User Tampering): Livewire rejects mutating #[Locked] registrationId.
     */
    public function test_target_b_cross_user_cannot_tamper_locked_registration_id(): void
    {
        $owner = $this->createParticipant();
        $attacker = $this->createParticipant();
        [$event, $category, $registrationB] = $this->createEventWithRegistration($owner);

        $component = Livewire::actingAs($attacker)
            ->test(Checkout::class, [
                'eventSlug' => $event->slug,
                'categoryId' => $category->id,
            ]);

        $threw = false;
        try {
            $component->set('registrationId', $registrationB->id);
        } catch (\Throwable $e) {
            $threw = true;
        }
        $this->assertTrue($threw, 'registrationId must be locked from client-side tampering');

        // Furthermore, scoped registration() method returns null for another participant's registration
        $checkout = app(Checkout::class);
        $checkout->registrationId = $registrationB->id;
        $this->assertNull($checkout->registration());
    }

    // =========================================================================
    // TARGET C: Payment Completion Path (Checkout::simulatePaid & CompleteRegistrationAction)
    // =========================================================================

    /**
     * Target C (Positive Control): Owner can complete payment on own pending registration.
     */
    public function test_target_c_positive_control_owner_can_complete_payment(): void
    {
        config(['payments.simulation' => true]);

        $owner = $this->createParticipant();
        $penyelenggara = $this->createPenyelenggara();
        $event = Event::factory()->create(['user_id' => $penyelenggara->id]);

        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Half Fondo',
            'code' => 'HF',
            'distance_km' => 60,
            'elevation_gain_m' => 600,
            'price' => 300000,
            'quota' => 5,
            'registered_count' => 0,
            'wave_start_time' => 'Wave 1',
            'cut_off_hours' => 4,
        ]);

        $registration = Registration::create([
            'user_id' => $owner->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Owner Pending',
            'identity_number' => '3308012408980005',
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Kontak',
            'emergency_contact_relation' => 'Keluarga',
            'emergency_contact_phone' => '08123456789',
            'status' => Registration::STATUS_PENDING,
        ]);

        $component = Livewire::actingAs($owner)
            ->test(Checkout::class, [
                'eventSlug' => $event->slug,
                'categoryId' => $category->id,
            ]);

        $component->call('simulatePaid');

        $registration->refresh();
        $this->assertSame(Registration::STATUS_PAID, $registration->status);
        $this->assertNotNull($registration->bib_number);
    }

    /**
     * Target C (Cross-User): Participant A cannot trigger payment for Participant B's registration.
     */
    public function test_target_c_cross_user_cannot_trigger_payment_for_other_participant(): void
    {
        config(['payments.simulation' => true]);

        $ownerB = $this->createParticipant();
        $attackerA = $this->createParticipant();
        $penyelenggara = $this->createPenyelenggara();
        $event = Event::factory()->create(['user_id' => $penyelenggara->id]);

        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Half Fondo',
            'code' => 'HF2',
            'distance_km' => 60,
            'elevation_gain_m' => 600,
            'price' => 300000,
            'quota' => 5,
            'registered_count' => 0,
            'wave_start_time' => 'Wave 1',
            'cut_off_hours' => 4,
        ]);

        $registrationB = Registration::create([
            'user_id' => $ownerB->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Peserta B Pending',
            'identity_number' => '3308012408980009',
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Kontak B',
            'emergency_contact_relation' => 'Keluarga',
            'emergency_contact_phone' => '08123456789',
            'status' => Registration::STATUS_PENDING,
        ]);

        // Attacker A calls simulatePaid() on the event category
        $component = Livewire::actingAs($attackerA)
            ->test(Checkout::class, [
                'eventSlug' => $event->slug,
                'categoryId' => $category->id,
            ]);

        $component->call('simulatePaid');

        // Attacker gets error because Attacker has no registration for this category
        $component->assertSet('error', 'Data pendaftaran tidak ditemukan. Ulangi langkah data diri.');

        // Participant B's registration remains pending and untouched
        $registrationB->refresh();
        $this->assertSame(Registration::STATUS_PENDING, $registrationB->status);
        $this->assertNull($registrationB->bib_number);
    }

    // =========================================================================
    // TARGET D: Filament Registrations Resource (/admin/registrations)
    // =========================================================================

    /**
     * Target D (Positive Control): Penyelenggara can access registration for own event in Filament.
     */
    public function test_target_d_positive_control_penyelenggara_can_view_own_event_registration(): void
    {
        $penyelenggaraOwner = $this->createPenyelenggara();
        $participant = $this->createParticipant();
        [, , $registration] = $this->createEventWithRegistration($participant, $penyelenggaraOwner);

        $response = $this->actingAs($penyelenggaraOwner)->get('/admin/registrations/' . $registration->id . '/edit');

        $response->assertOk();
    }

    /**
     * Target D (Cross-User): Penyelenggara cannot access registration for other organizer's event (404).
     */
    public function test_target_d_cross_user_penyelenggara_cannot_view_foreign_event_registration(): void
    {
        $penyelenggaraOwner = $this->createPenyelenggara();
        $penyelenggaraOther = $this->createPenyelenggara();
        $participant = $this->createParticipant();
        [, , $registration] = $this->createEventWithRegistration($participant, $penyelenggaraOwner);

        $response = $this->actingAs($penyelenggaraOther)->get('/admin/registrations/' . $registration->id . '/edit');

        $response->assertNotFound();
    }

    // =========================================================================
    // TARGET EXTRA: Participant Helpdesk Ticket Isolation (/dashboard/bantuan/{ticket})
    // =========================================================================

    /**
     * Helpdesk (Positive Control): Reporter can view own helpdesk ticket.
     */
    public function test_helpdesk_positive_control_reporter_can_view_own_ticket(): void
    {
        $reporter = $this->createParticipant();
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'subject' => 'Kendala Tiket Saya',
        ]);

        $response = $this->actingAs($reporter)->get('/dashboard/bantuan/' . $ticket->id);

        $response->assertOk();
    }

    /**
     * Helpdesk (Cross-User): Other participant cannot view foreign helpdesk ticket (404).
     */
    public function test_helpdesk_cross_user_other_participant_cannot_view_foreign_ticket(): void
    {
        $reporter = $this->createParticipant();
        $attacker = $this->createParticipant();
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'subject' => 'Kendala Rahasia B',
        ]);

        $response = $this->actingAs($attacker)->get('/dashboard/bantuan/' . $ticket->id);

        $response->assertNotFound();
    }

    /**
     * Helpdesk Operator Route: Non-operator/non-admin participant cannot access operator detail (302 redirect).
     */
    public function test_helpdesk_operator_route_blocked_for_participant(): void
    {
        $reporter = $this->createParticipant();
        $attacker = $this->createParticipant();
        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
        ]);

        $response = $this->actingAs($attacker)->get('/helpdesk/tickets/' . $ticket->id);

        $response->assertRedirect('/dashboard');
    }
}
