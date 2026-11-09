<?php

namespace Database\Seeders;

use App\Models\FinancialLedger;
use App\Models\Organization;
use App\Models\PayoutRequest;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $bromoOrg = Organization::where('slug', 'bromo-cycling-club')->first();
        if (! $bromoOrg) {
            return;
        }

        $admin = User::where('email', 'admin@velosha.id')->first();
        $owner = User::where('email', 'penyelenggara@velosha.id')->first();
        $primaryAccount = $bromoOrg->payoutAccounts()->where('is_primary', true)->first()
            ?? $bromoOrg->payoutAccounts()->first();

        // 1. Catat kredit penjualan tiket dari pendaftaran yang sudah lunas
        $paidRegistrations = Registration::whereIn('status', [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN])
            ->whereHas('category.event', fn ($q) => $q->where('organization_id', $bromoOrg->id))
            ->take(60)
            ->get();

        foreach ($paidRegistrations as $reg) {
            $exists = FinancialLedger::where('registration_id', $reg->id)->exists();
            if (! $exists) {
                FinancialLedger::create([
                    'organization_id' => $bromoOrg->id,
                    'event_id' => $reg->category->event_id,
                    'registration_id' => $reg->id,
                    'type' => 'ticket_sale',
                    'direction' => 'credit',
                    'amount' => (int) $reg->category->price,
                    'note' => 'Penjualan tiket '.$reg->category->name.' - '.$reg->participant_name,
                    'created_at' => $reg->created_at ?? now()->subDays(rand(1, 10)),
                ]);
            }
        }

        // Jika saldo masih di bawah Rp 10.000.000, tambahkan kredit awal batch penjualan
        if ($bromoOrg->financialBalance() < 10000000) {
            FinancialLedger::create([
                'organization_id' => $bromoOrg->id,
                'event_id' => $bromoOrg->events()->first()?->id,
                'type' => 'ticket_sale',
                'direction' => 'credit',
                'amount' => 12500000,
                'note' => 'Penjualan Batch 1 Early Bird - Velosha Gran Fondo Borobudur',
                'created_at' => now()->subDays(10),
            ]);
        }

        // 2. Buat 1 Payout Request yang sudah disetujui (Disetujui di masa lalu dengan bukti transfer)
        if ($primaryAccount && ! PayoutRequest::where('organization_id', $bromoOrg->id)->where('status', 'approved')->exists()) {
            PayoutRequest::create([
                'organization_id' => $bromoOrg->id,
                'payout_account_id' => $primaryAccount->id,
                'amount' => 5000000,
                'status' => 'approved',
                'requested_by' => $owner?->id ?? $admin?->id,
                'reviewed_by' => $admin?->id,
                'reviewed_at' => now()->subDays(3),
                'reference_number' => 'TRF-BCA-981023',
            ]);

            FinancialLedger::create([
                'organization_id' => $bromoOrg->id,
                'type' => 'payout',
                'direction' => 'debit',
                'amount' => 5000000,
                'note' => 'Pencairan dana ref: TRF-BCA-981023 ke rekening BCA',
                'created_at' => now()->subDays(3),
            ]);
        }

        // 3. Buat 1 Payout Request status "pending" agar Admin bisa langsung mengujinya di /admin/payout-requests
        if ($primaryAccount && ! PayoutRequest::where('organization_id', $bromoOrg->id)->where('status', 'pending')->exists()) {
            PayoutRequest::create([
                'organization_id' => $bromoOrg->id,
                'payout_account_id' => $primaryAccount->id,
                'amount' => 3500000,
                'status' => 'pending',
                'requested_by' => $owner?->id ?? $admin?->id,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'reference_number' => null,
            ]);
        }
    }
}
