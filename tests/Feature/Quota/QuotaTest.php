<?php

namespace Tests\Feature\Quota;

use App\Models\Event;
use App\Models\Organization;
use App\Models\QuotaLedger;
use App\Models\QuotaPackage;
use App\Models\QuotaPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotaTest extends TestCase
{
    use RefreshDatabase;

    protected function createOrganizationWithMember(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);
        $organization = Organization::create([
            'name' => 'Brompton Bandung Club',
            'slug' => 'brompton-bandung-club',
            'description' => 'Komunitas sepeda lipat Bandung.',
            'verification_status' => Organization::VERIFICATION_VERIFIED,
        ]);

        $organization->users()->attach($user->id, ['role' => 'owner']);

        return [$organization, $user];
    }

    public function test_organization_quota_balance_is_calculated_from_ledger(): void
    {
        [$organization] = $this->createOrganizationWithMember();

        $this->assertSame(0, $organization->quotaBalance());
        $this->assertFalse($organization->hasQuota());

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 3,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $this->assertSame(3, $organization->quotaBalance());
        $this->assertTrue($organization->hasQuota());

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => -1,
            'reason' => 'publish_event',
            'created_at' => now(),
        ]);

        $this->assertSame(2, $organization->quotaBalance());
    }

    public function test_organizer_can_purchase_quota_package_via_mock_and_increments_balance(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        $package = QuotaPackage::create([
            'name' => 'Komunitas Pack',
            'events_count' => 3,
            'price' => 350000,
            'description' => '3 Kuota Event',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('organizations.quota.purchase', ['organization' => $organization->slug]), [
            'package_id' => $package->id,
        ]);

        $response->assertRedirect(route('organizations.quota', ['organization' => $organization->slug]));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('quota_purchases', [
            'organization_id' => $organization->id,
            'quota_package_id' => $package->id,
            'amount' => 350000,
            'status' => QuotaPurchase::STATUS_PAID,
        ]);

        $this->assertSame(3, $organization->quotaBalance());
    }

    public function test_webhook_settlement_marks_quota_purchase_paid_and_increments_ledger(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        $package = QuotaPackage::create([
            'name' => 'Pro EO Pack',
            'events_count' => 10,
            'price' => 950000,
            'is_active' => true,
        ]);

        $purchase = QuotaPurchase::create([
            'organization_id' => $organization->id,
            'quota_package_id' => $package->id,
            'user_id' => $user->id,
            'order_id' => 'VLS-QUOTA-TEST-001',
            'amount' => 950000,
            'status' => QuotaPurchase::STATUS_PENDING,
        ]);

        $response = $this->postJson(route('payment.midtrans.webhook'), [
            'order_id' => 'VLS-QUOTA-TEST-001',
            'status_code' => '200',
            'gross_amount' => '950000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);

        $purchase->refresh();
        $this->assertSame(QuotaPurchase::STATUS_PAID, $purchase->status);
        $this->assertSame(10, $organization->quotaBalance());
    }

    public function test_event_publishing_deducts_one_quota_from_organization(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        // Beri 2 kuota ke organisasi
        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 2,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_DRAFT,
        ]);

        $this->assertSame(2, $organization->quotaBalance());

        // Publish event
        $event->update(['status' => Event::STATUS_PUBLISHED]);

        $this->assertSame(1, $organization->quotaBalance());
        $this->assertDatabaseHas('quota_ledger', [
            'organization_id' => $organization->id,
            'delta' => -1,
            'reason' => 'publish_event',
            'event_id' => $event->id,
        ]);

        // Simpan ulang tanpa ganti status tidak boleh memotong kuota lagi
        $event->update(['title' => 'Judul Baru Setelah Publish']);
        $this->assertSame(1, $organization->quotaBalance());
    }

    public function test_event_publishing_is_rejected_when_organization_has_zero_quota(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        $this->assertSame(0, $organization->quotaBalance());

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_DRAFT,
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Kuota event organisasi Anda tidak mencukupi (0)');

        $event->update(['status' => Event::STATUS_PUBLISHED]);
    }

    public function test_non_member_cannot_access_or_purchase_quota(): void
    {
        [$organization] = $this->createOrganizationWithMember();
        $stranger = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $package = QuotaPackage::create([
            'name' => 'Single Event',
            'events_count' => 1,
            'price' => 150000,
            'is_active' => true,
        ]);

        $this->actingAs($stranger);

        $response = $this->get(route('organizations.quota', ['organization' => $organization->slug]));
        $response->assertStatus(403);

        $buyResponse = $this->post(route('organizations.quota.purchase', ['organization' => $organization->slug]), [
            'package_id' => $package->id,
        ]);
        $buyResponse->assertStatus(403);
    }

    public function test_event_created_directly_as_published_deducts_quota_with_valid_event_id(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $this->assertSame(0, $organization->quotaBalance());
        $this->assertDatabaseHas('quota_ledger', [
            'organization_id' => $organization->id,
            'delta' => -1,
            'reason' => 'publish_event',
            'event_id' => $event->id,
        ]);
    }

    public function test_cancelling_published_event_without_tickets_refunds_quota(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $this->assertSame(0, $organization->quotaBalance());

        // Batalkan event yang belum ada penjualan tiket
        $event->update(['status' => Event::STATUS_CANCELLED]);

        // Kuota kembali ke organisasi!
        $this->assertSame(1, $organization->quotaBalance());
        $this->assertDatabaseHas('quota_ledger', [
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'refund_event',
            'event_id' => $event->id,
        ]);
    }

    public function test_cancelling_event_with_paid_tickets_does_not_refund_quota(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $category = \App\Models\EventCategory::create([
            'event_id' => $event->id,
            'name' => '100K Gran Fondo',
            'code' => 'GF100',
            'distance_km' => 100,
            'elevation_gain_m' => 850,
            'price' => 350000,
            'quota' => 50,
            'registered_count' => 1,
            'wave_start_time' => '06:00',
            'cut_off_hours' => 6.0,
        ]);

        \App\Models\Registration::create([
            'event_category_id' => $category->id,
            'user_id' => $user->id,
            'participant_name' => 'John Doe',
            'identity_number' => '3171010101010001',
            'gender' => 'male',
            'birth_date' => '1995-01-01',
            'blood_type' => 'O',
            'phone' => '081234567890',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_relation' => 'Keluarga',
            'emergency_contact_phone' => '081234567891',
            'status' => \App\Models\Registration::STATUS_PAID,
            'bib_number' => 'GF-101',
        ]);

        $this->assertSame(0, $organization->quotaBalance());

        // Batalkan event yang SUDAH ada tiket terjual
        $event->update(['status' => Event::STATUS_CANCELLED]);

        // Kuota tetap 0 (hangus, tidak di-refund)
        $this->assertSame(0, $organization->quotaBalance());
        $this->assertDatabaseMissing('quota_ledger', [
            'organization_id' => $organization->id,
            'reason' => 'refund_event',
            'event_id' => $event->id,
        ]);
    }

    public function test_republishing_cancelled_event_deducts_quota_again(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $this->assertSame(0, $organization->quotaBalance());

        // Cancel
        $event->update(['status' => Event::STATUS_CANCELLED]);
        $this->assertSame(1, $organization->quotaBalance());

        // Re-publish
        $event->update(['status' => Event::STATUS_PUBLISHED]);
        $this->assertSame(0, $organization->quotaBalance());

        $this->assertSame(2, $organization->quotaLedger()->where('reason', 'publish_event')->count());
        $this->assertSame(1, $organization->quotaLedger()->where('reason', 'refund_event')->count());
    }

    public function test_mark_as_paid_is_idempotent_and_prevents_duplicate_ledger(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();
        $package = QuotaPackage::create([
            'name' => 'Paket Pro (3 Event)',
            'events_count' => 3,
            'price' => 750000,
            'is_active' => true,
        ]);

        $purchase = QuotaPurchase::create([
            'organization_id' => $organization->id,
            'quota_package_id' => $package->id,
            'user_id' => $user->id,
            'order_id' => 'VLS-QUOTA-TEST-DUP-1',
            'amount' => $package->price,
            'status' => QuotaPurchase::STATUS_PENDING,
        ]);

        // Panggilan pertama
        $purchase->markAsPaid('midtrans', ['transaction_id' => 'tx-1']);
        $this->assertSame(3, $organization->quotaBalance());

        // Panggilan kedua (misal webhook ganda / replay)
        $purchase->markAsPaid('midtrans', ['transaction_id' => 'tx-1']);
        $this->assertSame(3, $organization->quotaBalance());

        $this->assertSame(1, QuotaLedger::where('quota_purchase_id', $purchase->id)->count());
    }
}

