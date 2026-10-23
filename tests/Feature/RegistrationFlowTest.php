<?php

namespace Tests\Feature;

use App\Livewire\Checkout;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
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

    private function payload(User $user): array
    {
        return [
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
        ];
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

    private function paidRegistration(User $user, EventCategory $category): Registration
    {
        $registration = $this->pendingRegistration($user, $category);
        $registration->bib_number = Registration::generateBibNumber($category);
        $registration->status = Registration::STATUS_PAID;
        $registration->save();

        $category->increment('registered_count');
        Ticket::issueFor($registration);

        return $registration;
    }

    public function test_guest_must_login_to_open_checkout(): void
    {
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_admin_cannot_open_checkout(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/checkout')->assertRedirect($admin->home());
    }

    public function test_full_flow_from_category_selection_to_paid_ticket(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->assertSet('step', 1)
            ->call('toStep2')
            ->assertSet('step', 2)
            ->set($this->payload($user))
            ->call('toStep3')
            ->assertSet('step', 3)
            ->assertHasNoErrors();

        $registration = Registration::sole();
        $this->assertSame(Registration::STATUS_PENDING, $registration->status);
        $this->assertSame($user->id, $registration->user_id);
        $this->assertSame($category->id, $registration->event_category_id);

        config(['payments.simulation' => true]);

        $component->call('openPayment')
            ->assertSet('showPaymentModal', true)
            ->call('simulatePaid');

        $registration->refresh();
        $component->assertRedirect('/ticket/'.$registration->id);

        $this->assertSame(Registration::STATUS_PAID, $registration->status);
        $this->assertMatchesRegularExpression('/^VLS-GF-\d{4}$/', $registration->bib_number);

        $ticket = Ticket::where('registration_id', $registration->id)->sole();
        $this->assertStringStartsWith('VELOSHA-VERIFY-', $ticket->qr_code_data);
        $this->assertStringContainsString($registration->bib_number, $ticket->qr_code_data);

        $this->assertSame(1, $category->refresh()->registered_count);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
            'phone' => '081223344556',
        ]);
    }

    public function test_payment_method_can_switch_inside_simulation_modal(): void
    {
        config(['payments.simulation' => true]);

        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->call('toStep2')
            ->set($this->payload($user))
            ->call('toStep3')
            ->call('openPayment')
            ->assertSet('paymentMethod', 'qris')
            ->assertSee('Pindai QRIS')
            ->call('selectPaymentMethod', 'bca_va')
            ->assertSet('paymentMethod', 'bca_va')
            ->assertSee('Virtual Account BCA')
            ->call('selectPaymentMethod', 'mandiri')
            ->assertSet('paymentMethod', 'mandiri')
            ->assertSee('Bill Key')
            ->call('selectPaymentMethod', 'paypal')
            ->assertSet('paymentMethod', 'mandiri');
    }

    public function test_minor_is_rejected_on_race_day_rule(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->call('toStep2')->assertSet('step', 2);

        $payload = $this->payload($user);
        $payload['birth_date'] = now()->subYears(16)->format('Y-m-d');

        $component->set($payload)->call('toStep3')->assertHasErrors('birth_date');

        $this->assertSame(2, $component->get('step'));
        $this->assertSame(0, Registration::count());
    }

    public function test_missing_required_fields_fail_validation(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->call('toStep2')->call('toStep3');

        $component->assertHasErrors([
            'identity_number',
            'birth_date',
            'phone',
            'emergency_contact_name',
            'emergency_contact_phone',
        ]);

        $this->assertSame(0, Registration::count());
    }

    public function test_pending_registration_resumes_at_step_three(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $registration = $this->pendingRegistration($user, $category);

        $this->actingAs($user);

        Livewire::test(Checkout::class, ['eventSlug' => $event->slug])
            ->assertSet('step', 3)
            ->assertSet('registrationId', $registration->id)
            ->assertSet('categoryId', $category->id);
    }

    public function test_paid_registration_stays_on_step_one_on_mount(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $registration = $this->paidRegistration($user, $category);

        $this->actingAs($user);

        Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ])
            ->assertSet('step', 1)
            ->assertSet('resumed', false)
            ->assertSee('Anda sudah terdaftar di kategori ini')
            ->assertSee('Lihat E-Ticket');
    }

    public function test_paid_category_blocks_to_step_two_with_error(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $this->paidRegistration($user, $category);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->call('toStep2')
            ->assertSet('step', 1)
            ->assertSee('Anda sudah terdaftar di kategori ini');
    }

    public function test_pending_resume_banner_can_reset_to_start(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $this->pendingRegistration($user, $category);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, ['eventSlug' => $event->slug])
            ->assertSet('step', 3)
            ->assertSet('resumed', true)
            ->assertSee('Melanjutkan pendaftaran yang belum selesai')
            ->call('resetToStart')
            ->assertSet('step', 1)
            ->assertSet('resumed', false)
            ->assertSet('registrationId', null);
    }

    public function test_full_quota_blocks_progress_at_step_two(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event, ['quota' => 1, 'registered_count' => 1]);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        $component->call('toStep2')
            ->assertSet('step', 1)
            ->assertSee('Slot kategori ini sudah penuh');
    }

    public function test_payment_when_quota_ran_out_cancels_registration(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event, ['quota' => 1, 'registered_count' => 1]);
        $registration = $this->pendingRegistration($user, $category);

        $this->actingAs($user);

        $component = Livewire::test(Checkout::class, [
            'eventSlug' => $event->slug,
            'categoryId' => $category->id,
        ]);

        config(['payments.simulation' => true]);

        $component->assertSet('step', 3)->call('simulatePaid');

        $registration->refresh();
        $this->assertSame(Registration::STATUS_CANCELLED, $registration->status);
        $this->assertNull($registration->bib_number);
        $this->assertDatabaseMissing('tickets', ['registration_id' => $registration->id]);
        $component->assertSee('Slot kategori sudah penuh');
    }

    public function test_ticket_detail_page_is_owner_only(): void
    {
        $owner = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $registration = $this->paidRegistration($owner, $category);

        $this->get('/ticket/'.$registration->id)->assertRedirect('/login');

        $this->actingAs($owner);
        $this->get('/ticket/'.$registration->id)
            ->assertOk()
            ->assertSee($registration->bib_number)
            ->assertSee($registration->participant_name)
            ->assertDontSee('/admin/registrations', false);

        $this->get('/ticket')->assertOk()->assertSee($registration->bib_number);

        $other = User::factory()->create(['role' => 'peserta']);
        $this->actingAs($other)->get('/ticket/'.$registration->id)->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/ticket/'.$registration->id)
            ->assertOk()
            ->assertSee('/admin/registrations', false);
    }

    public function test_ticket_page_without_registration_shows_empty_state(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);

        $this->actingAs($user);

        $this->get('/ticket')
            ->assertOk()
            ->assertSee('Belum Ada E-Ticket')
            ->assertSee('Jelajahi Kalender Balap');
    }

    public function test_ticket_page_shows_pending_payment_banner(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $this->pendingRegistration($user, $category);

        $this->actingAs($user);

        $this->get('/ticket')
            ->assertOk()
            ->assertSee('Menunggu Pembayaran')
            ->assertSee('Bayar Sekarang')
            ->assertSee('REG-');
    }

    public function test_dashboard_lists_my_tickets_and_stats(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event);
        $registration = $this->paidRegistration($user, $category);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Tiket & BIB Saya')
            ->assertSee($registration->bib_number)
            ->assertSee('Lunas & Aktif');
    }

    public function test_event_detail_cta_opens_role_warning_for_admin(): void
    {
        $event = $this->makeEvent();
        $this->makeCategory($event);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $this->get('/events/'.$event->slug)
            ->assertOk()
            ->assertSee('roleWarning = true', false)
            ->assertSee('AKUN TIDAK SESUAI')
            ->assertDontSee('id="checkout-cta"', false);
    }

    public function test_event_detail_cta_links_checkout_for_peserta_and_guest(): void
    {
        $event = $this->makeEvent();
        $this->makeCategory($event);
        $peserta = User::factory()->create(['role' => 'peserta']);

        $this->actingAs($peserta);
        $this->get('/events/'.$event->slug)
            ->assertOk()
            ->assertSee('id="checkout-cta"', false)
            ->assertDontSee('roleWarning = true', false);

        $this->flushSession();
        $this->app['auth']->forgetGuards();
        $this->get('/events/'.$event->slug)
            ->assertOk()
            ->assertSee('id="checkout-cta"', false)
            ->assertDontSee('roleWarning = true', false);
    }
}
