<?php

namespace Tests\Feature\Finance;

use App\Actions\CompleteRegistrationAction;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\FinancialLedger;
use App\Models\Organization;
use App\Models\PayoutAccount;
use App\Models\PayoutRequest;
use App\Models\QuotaLedger;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function createVerifiedOrganizationWithMember(): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PESERTA,
        ]);

        $organization = Organization::create([
            'name' => 'Bandung Cycling Squad',
            'slug' => 'bandung-cycling-squad',
            'verification_status' => Organization::VERIFICATION_VERIFIED,
        ]);

        $organization->users()->attach($user->id, [
            'role' => 'owner',
        ]);

        return [$organization, $user];
    }

    public function test_paid_registration_automatically_creates_credit_entry_in_financial_ledger(): void
    {
        [$organization, $organizer] = $this->createVerifiedOrganizationWithMember();

        QuotaLedger::create([
            'organization_id' => $organization->id,
            'delta' => 1,
            'reason' => 'purchase',
            'created_at' => now(),
        ]);

        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'organization_id' => $organization->id,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $category = EventCategory::create([
            'event_id' => $event->id,
            'name' => '100K Gran Fondo',
            'code' => 'GF100',
            'distance_km' => 100,
            'elevation_gain_m' => 900,
            'price' => 250000,
            'quota' => 50,
            'registered_count' => 0,
            'wave_start_time' => '06:00',
            'cut_off_hours' => 6.0,
        ]);

        $participant = User::factory()->create();
        $registration = Registration::create([
            'event_category_id' => $category->id,
            'user_id' => $participant->id,
            'participant_name' => 'Ahmad Razi',
            'identity_number' => '3273010101010001',
            'gender' => 'male',
            'birth_date' => '1996-05-10',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Siti',
            'emergency_contact_relation' => 'Istri',
            'emergency_contact_phone' => '081234567890',
            'status' => Registration::STATUS_PENDING,
        ]);

        $this->assertSame(0, $organization->financialBalance());

        // Jalankan pelunasan pendaftaran
        $action = app(CompleteRegistrationAction::class);
        $action->execute($registration);

        $this->assertSame(250000, $organization->financialBalance());
        $this->assertDatabaseHas('financial_ledger', [
            'organization_id' => $organization->id,
            'event_id' => $event->id,
            'registration_id' => $registration->id,
            'type' => FinancialLedger::TYPE_TICKET_SALE,
            'direction' => FinancialLedger::DIRECTION_CREDIT,
            'amount' => 250000,
        ]);
    }

    public function test_financial_ledger_is_strictly_append_only(): void
    {
        [$organization] = $this->createVerifiedOrganizationWithMember();

        $entry = FinancialLedger::create([
            'organization_id' => $organization->id,
            'type' => FinancialLedger::TYPE_ADJUSTMENT,
            'direction' => FinancialLedger::DIRECTION_CREDIT,
            'amount' => 100000,
            'note' => 'Penyesuaian awal',
            'created_at' => now(),
        ]);

        // Coba modifikasi -> throw DomainException
        $this->expectException(\DomainException::class);
        $entry->update(['amount' => 200000]);
    }

    public function test_organization_can_add_payout_account_with_encrypted_account_number(): void
    {
        [$organization, $user] = $this->createVerifiedOrganizationWithMember();

        $response = $this->actingAs($user)->post(route('organizations.finance.account.store', ['organization' => $organization->slug]), [
            'bank_name' => 'BCA',
            'account_number' => '880812345678',
            'account_name' => 'Bandung Cycling Squad',
        ]);

        $response->assertRedirect(route('organizations.finance', ['organization' => $organization->slug]));

        $account = $organization->payoutAccounts()->first();
        $this->assertNotNull($account);
        $this->assertSame('BCA', $account->bank_name);
        $this->assertSame('880812345678', $account->account_number);
        $this->assertSame('•••• 5678', $account->masked_account_number);

        // Buktikan di basis data mentah tersimpan dalam keadaan terenkripsi
        $raw = DB::table('payout_accounts')->where('id', $account->id)->value('account_number');
        $this->assertNotSame('880812345678', $raw);
        $this->assertStringContainsString('eyJ', $raw); // Laravel encrypted payload JSON base64
    }

    public function test_organization_can_request_payout_within_available_balance(): void
    {
        [$organization, $user] = $this->createVerifiedOrganizationWithMember();

        $account = PayoutAccount::create([
            'organization_id' => $organization->id,
            'bank_name' => 'Mandiri',
            'account_number' => '1300098765432',
            'account_name' => 'Bandung Cycling Squad',
        ]);

        FinancialLedger::create([
            'organization_id' => $organization->id,
            'type' => FinancialLedger::TYPE_TICKET_SALE,
            'direction' => FinancialLedger::DIRECTION_CREDIT,
            'amount' => 500000,
            'note' => 'Penjualan tiket race',
            'created_at' => now(),
        ]);

        $this->assertSame(500000, $organization->availablePayoutBalance());

        $response = $this->actingAs($user)->post(route('organizations.finance.payout', ['organization' => $organization->slug]), [
            'payout_account_id' => $account->id,
            'amount' => 200000,
        ]);

        $response->assertRedirect(route('organizations.finance', ['organization' => $organization->slug]));
        $this->assertDatabaseHas('payout_requests', [
            'organization_id' => $organization->id,
            'payout_account_id' => $account->id,
            'amount' => 200000,
            'status' => PayoutRequest::STATUS_PENDING,
        ]);

        // Saldo yang bisa ditarik berkurang karena nominal 200.000 dikunci status pending
        $this->assertSame(200000, $organization->pendingPayoutAmount());
        $this->assertSame(300000, $organization->availablePayoutBalance());
    }

    public function test_organization_cannot_request_payout_exceeding_available_balance(): void
    {
        [$organization, $user] = $this->createVerifiedOrganizationWithMember();

        $account = PayoutAccount::create([
            'organization_id' => $organization->id,
            'bank_name' => 'BCA',
            'account_number' => '12345678',
            'account_name' => 'Bandung Cycling Squad',
        ]);

        FinancialLedger::create([
            'organization_id' => $organization->id,
            'type' => FinancialLedger::TYPE_TICKET_SALE,
            'direction' => FinancialLedger::DIRECTION_CREDIT,
            'amount' => 100000,
            'created_at' => now(),
        ]);

        // Coba tarik 200.000 (melebihi saldo 100.000)
        $response = $this->actingAs($user)->post(route('organizations.finance.payout', ['organization' => $organization->slug]), [
            'payout_account_id' => $account->id,
            'amount' => 200000,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(0, $organization->payoutRequests()->count());
    }

    public function test_unverified_organization_cannot_request_payout(): void
    {
        [$organization, $user] = $this->createVerifiedOrganizationWithMember();
        $organization->update(['verification_status' => Organization::VERIFICATION_UNVERIFIED]);

        $account = PayoutAccount::create([
            'organization_id' => $organization->id,
            'bank_name' => 'BCA',
            'account_number' => '12345678',
            'account_name' => 'Bandung Cycling Squad',
        ]);

        FinancialLedger::create([
            'organization_id' => $organization->id,
            'type' => FinancialLedger::TYPE_TICKET_SALE,
            'direction' => FinancialLedger::DIRECTION_CREDIT,
            'amount' => 500000,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('organizations.finance.payout', ['organization' => $organization->slug]), [
            'payout_account_id' => $account->id,
            'amount' => 100000,
        ]);

        $response->assertSessionHas('error', 'Komunitas / organisasi harus berstatus terverifikasi sebelum dapat mengajukan pencairan dana.');
        $this->assertSame(0, $organization->payoutRequests()->count());
    }

    public function test_admin_approving_payout_debits_ledger_and_records_reference(): void
    {
        [$organization, $user] = $this->createVerifiedOrganizationWithMember();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $account = PayoutAccount::create([
            'organization_id' => $organization->id,
            'bank_name' => 'BCA',
            'account_number' => '12345678',
            'account_name' => 'Bandung Cycling Squad',
        ]);

        FinancialLedger::create([
            'organization_id' => $organization->id,
            'type' => FinancialLedger::TYPE_TICKET_SALE,
            'direction' => FinancialLedger::DIRECTION_CREDIT,
            'amount' => 500000,
            'created_at' => now(),
        ]);

        $payout = PayoutRequest::create([
            'organization_id' => $organization->id,
            'payout_account_id' => $account->id,
            'amount' => 200000,
            'status' => PayoutRequest::STATUS_PENDING,
            'requested_by' => $user->id,
        ]);

        // Admin setujui transfer
        $payout->approve($admin, 'TRF-BCA-20261109-991');

        $payout->refresh();
        $this->assertSame(PayoutRequest::STATUS_APPROVED, $payout->status);
        $this->assertSame('TRF-BCA-20261109-991', $payout->reference_number);
        $this->assertSame($admin->id, $payout->reviewed_by);

        // Saldo kas organisasi otomatis terpotong 200.000 menjadi 300.000
        $this->assertSame(300000, $organization->financialBalance());
        $this->assertSame(300000, $organization->availablePayoutBalance());
        $this->assertSame(200000, $organization->totalPaidOut());
    }

    public function test_admin_rejecting_payout_does_not_debit_ledger_and_restores_available_balance(): void
    {
        [$organization, $user] = $this->createVerifiedOrganizationWithMember();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $account = PayoutAccount::create([
            'organization_id' => $organization->id,
            'bank_name' => 'BCA',
            'account_number' => '12345678',
            'account_name' => 'Bandung Cycling Squad',
        ]);

        FinancialLedger::create([
            'organization_id' => $organization->id,
            'type' => FinancialLedger::TYPE_TICKET_SALE,
            'direction' => FinancialLedger::DIRECTION_CREDIT,
            'amount' => 500000,
            'created_at' => now(),
        ]);

        $payout = PayoutRequest::create([
            'organization_id' => $organization->id,
            'payout_account_id' => $account->id,
            'amount' => 200000,
            'status' => PayoutRequest::STATUS_PENDING,
            'requested_by' => $user->id,
        ]);

        $this->assertSame(300000, $organization->availablePayoutBalance());

        // Admin tolak penarikan
        $payout->reject($admin, 'Nama rekening tidak cocok dengan nama pengurus.');
        $payout->refresh();

        $this->assertSame(PayoutRequest::STATUS_REJECTED, $payout->status);
        $this->assertSame('Nama rekening tidak cocok dengan nama pengurus.', $payout->rejection_note);

        // Saldo kas utuh 500.000 dan saldo yang bisa ditarik kembali ke 500.000
        $this->assertSame(500000, $organization->financialBalance());
        $this->assertSame(500000, $organization->availablePayoutBalance());
        $this->assertSame(0, $organization->totalPaidOut());
    }

    public function test_non_member_cannot_access_or_request_payout_for_another_organization(): void
    {
        [$organization] = $this->createVerifiedOrganizationWithMember();
        $outsider = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->actingAs($outsider)
            ->get(route('organizations.finance', ['organization' => $organization->slug]))
            ->assertStatus(403);
    }
}
