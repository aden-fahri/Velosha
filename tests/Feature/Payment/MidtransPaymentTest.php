<?php

namespace Tests\Feature\Payment;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function createRegistration(): Registration
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Gran Fondo',
            'code' => 'GF',
            'distance_km' => 130,
            'elevation_gain_m' => 1450,
            'price' => 250000,
            'quota' => 50,
            'registered_count' => 0,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 7,
        ]);

        return Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Peserta Uji Midtrans',
            'identity_number' => '3308012408980001',
            'birth_date' => '1995-05-15',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Kontak Darurat',
            'emergency_contact_relation' => 'Keluarga',
            'emergency_contact_phone' => '081234567890',
            'status' => Registration::STATUS_PENDING,
            'bib_number' => null,
        ]);
    }

    public function test_snap_transaction_creates_pending_payment_record(): void
    {
        $registration = $this->createRegistration();
        $service = app(MidtransService::class);

        $result = $service->createSnapTransaction($registration, 250000);

        $this->assertNotEmpty($result['order_id']);
        $this->assertNotEmpty($result['token']);
        $this->assertDatabaseHas('payments', [
            'registration_id' => $registration->id,
            'order_id' => $result['order_id'],
            'amount' => 250000,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function test_midtrans_webhook_settles_payment_and_issues_ticket(): void
    {
        $registration = $this->createRegistration();
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'order_id' => 'VLS-TEST-001',
            'amount' => 250000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->postJson(route('payment.midtrans.webhook'), [
            'order_id' => 'VLS-TEST-001',
            'status_code' => '200',
            'gross_amount' => '250000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);

        $payment->refresh();
        $registration->refresh();

        $this->assertSame(Payment::STATUS_SETTLEMENT, $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(Registration::STATUS_PAID, $registration->status);
        $this->assertNotNull($registration->bib_number);
        $this->assertNotNull($registration->ticket);
    }

    public function test_midtrans_webhook_is_idempotent(): void
    {
        $registration = $this->createRegistration();
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'order_id' => 'VLS-TEST-002',
            'amount' => 250000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $payload = [
            'order_id' => 'VLS-TEST-002',
            'status_code' => '200',
            'gross_amount' => '250000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ];

        // Hit pertama
        $this->postJson(route('payment.midtrans.webhook'), $payload)->assertStatus(200);

        // Hit kedua (duplikat notifikasi)
        $second = $this->postJson(route('payment.midtrans.webhook'), $payload);
        $second->assertStatus(200);
        $second->assertJson(['message' => 'Pembayaran sudah berstatus lunas sebelumnya.']);
    }

    public function test_midtrans_webhook_rejects_invalid_security_signature_when_configured(): void
    {
        config(['services.midtrans.server_key' => 'real-secret-key-123']);

        $registration = $this->createRegistration();
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'order_id' => 'VLS-TEST-003',
            'amount' => 100000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->postJson(route('payment.midtrans.webhook'), [
            'order_id' => 'VLS-TEST-003',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'completely-fake-signature',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
        $payment->refresh();
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
    }

    public function test_midtrans_webhook_cancels_registration_on_expire(): void
    {
        $registration = $this->createRegistration();
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'order_id' => 'VLS-TEST-004',
            'amount' => 150000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->postJson(route('payment.midtrans.webhook'), [
            'order_id' => 'VLS-TEST-004',
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'expire',
        ]);

        $response->assertStatus(200);
        $payment->refresh();
        $registration->refresh();

        $this->assertSame(Payment::STATUS_EXPIRE, $payment->status);
        $this->assertSame(Registration::STATUS_CANCELLED, $registration->status);
    }
}
