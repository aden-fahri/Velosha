<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\PayoutAccount;
use App\Models\QuotaLedger;
use App\Models\QuotaPackage;
use App\Models\QuotaPurchase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@velosha.id')->first();
        $ownerBromo = User::where('email', 'penyelenggara@velosha.id')->first();
        $managerBromo = User::where('email', 'manager@velosha.id')->first();
        $stafBromo = User::where('email', 'staf@velosha.id')->first();
        $ownerGravel = User::where('email', 'penyelenggara2@velosha.id')->first();

        // 1. Pastikan dummy file verifikasi ada di disk privat (disk 'local' points to storage/app/private)
        if (! app()->runningUnitTests()) {
            $privateDisk = Storage::disk('local');
            if (! $privateDisk->exists('verification_documents/ktp-bromo.pdf')) {
                $privateDisk->put('verification_documents/ktp-bromo.pdf', "%PDF-1.4 Dokumen Dummy KTP & Legalitas Bromo Cycling Club\n%%EOF");
            }
            if (! $privateDisk->exists('verification_documents/ktp-andi-gravel.pdf')) {
                $privateDisk->put('verification_documents/ktp-andi-gravel.pdf', "%PDF-1.4 Dokumen Dummy Legalitas Nusantara Gravel Explorers\n%%EOF");
            }
        }

        // 2. Organisasi 1: Bromo Cycling Club (Terverifikasi)
        $bromoOrg = Organization::updateOrCreate(
            ['slug' => 'bromo-cycling-club'],
            [
                'name' => 'Bromo Cycling Club',
                'description' => 'Komunitas balap sepeda jalan raya dan tanjakan berbasis di Jawa Timur.',
                'verification_status' => Organization::VERIFICATION_VERIFIED,
                'verification_document_path' => 'verification_documents/ktp-bromo.pdf',
                'verified_at' => now()->subDays(14),
                'verified_by' => $admin?->id,
            ]
        );

        // Anggota Bromo Cycling Club
        if ($ownerBromo) {
            OrganizationMember::updateOrCreate(
                ['organization_id' => $bromoOrg->id, 'user_id' => $ownerBromo->id],
                ['role' => 'owner']
            );
        }
        if ($managerBromo) {
            OrganizationMember::updateOrCreate(
                ['organization_id' => $bromoOrg->id, 'user_id' => $managerBromo->id],
                ['role' => 'manager']
            );
        }
        if ($stafBromo) {
            OrganizationMember::updateOrCreate(
                ['organization_id' => $bromoOrg->id, 'user_id' => $stafBromo->id],
                ['role' => 'staf']
            );
        }

        // Kuota Paket Bromo (Paket Pro 10 Event)
        $proPackage = QuotaPackage::where('name', 'Pro EO Pack')->first()
            ?? QuotaPackage::where('events_count', 10)->first();

        if ($proPackage && $ownerBromo) {
            $purchase = QuotaPurchase::firstOrCreate(
                ['order_id' => 'QTA-BROMO-001'],
                [
                    'organization_id' => $bromoOrg->id,
                    'quota_package_id' => $proPackage->id,
                    'user_id' => $ownerBromo->id,
                    'amount' => $proPackage->price,
                    'status' => 'paid',
                    'paid_at' => now()->subDays(14),
                ]
            );

            if (! QuotaLedger::where('organization_id', $bromoOrg->id)->where('reason', 'purchase')->exists()) {
                QuotaLedger::create([
                    'organization_id' => $bromoOrg->id,
                    'delta' => 10,
                    'reason' => 'purchase',
                    'quota_purchase_id' => $purchase->id,
                    'created_at' => now()->subDays(14),
                ]);
            }
        }

        // Rekening Payout Bromo Cycling Club
        PayoutAccount::firstOrCreate(
            ['organization_id' => $bromoOrg->id, 'bank_name' => 'BCA'],
            [
                'account_number' => '8830192831',
                'account_name' => 'Bromo Cycling Club',
                'is_primary' => true,
            ]
        );
        PayoutAccount::firstOrCreate(
            ['organization_id' => $bromoOrg->id, 'bank_name' => 'Mandiri'],
            [
                'account_number' => '1370019283719',
                'account_name' => 'Race Director Bromo',
                'is_primary' => false,
            ]
        );

        // 3. Organisasi 2: Nusantara Gravel Explorers (Pending Verifikasi)
        $gravelOrg = Organization::updateOrCreate(
            ['slug' => 'nusantara-gravel-explorers'],
            [
                'name' => 'Nusantara Gravel Explorers',
                'description' => 'Komunitas penjelajah rute gravel dan off-road petualangan nusantara.',
                'verification_status' => Organization::VERIFICATION_PENDING,
                'verification_document_path' => 'verification_documents/ktp-andi-gravel.pdf',
                'verified_at' => null,
                'verified_by' => null,
            ]
        );

        // Anggota Nusantara Gravel Explorers
        if ($ownerGravel) {
            OrganizationMember::updateOrCreate(
                ['organization_id' => $gravelOrg->id, 'user_id' => $ownerGravel->id],
                ['role' => 'owner']
            );
        }

        // Kuota Paket Gravel (Single Event Pack)
        $singlePackage = QuotaPackage::where('name', 'Single Event')->first()
            ?? QuotaPackage::where('events_count', 1)->first();

        if ($singlePackage && $ownerGravel) {
            $purchaseGravel = QuotaPurchase::firstOrCreate(
                ['order_id' => 'QTA-GRAVEL-001'],
                [
                    'organization_id' => $gravelOrg->id,
                    'quota_package_id' => $singlePackage->id,
                    'user_id' => $ownerGravel->id,
                    'amount' => $singlePackage->price,
                    'status' => 'paid',
                    'paid_at' => now()->subDays(2),
                ]
            );

            if (! QuotaLedger::where('organization_id', $gravelOrg->id)->where('reason', 'purchase')->exists()) {
                QuotaLedger::create([
                    'organization_id' => $gravelOrg->id,
                    'delta' => 1,
                    'reason' => 'purchase',
                    'quota_purchase_id' => $purchaseGravel->id,
                    'created_at' => now()->subDays(2),
                ]);
            }
        }

        // Rekening Payout Nusantara Gravel Explorers
        PayoutAccount::firstOrCreate(
            ['organization_id' => $gravelOrg->id, 'bank_name' => 'BNI'],
            [
                'account_number' => '0481928310',
                'account_name' => 'Andi Nusantara',
                'is_primary' => true,
            ]
        );
    }
}
