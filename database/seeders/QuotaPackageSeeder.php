<?php

namespace Database\Seeders;

use App\Models\QuotaPackage;
use Illuminate\Database\Seeder;

class QuotaPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Single Event',
                'events_count' => 1,
                'price' => 150000,
                'description' => 'Cocok untuk komunitas yang baru pertama kali menyelenggarakan race atau gowes bersama.',
                'is_active' => true,
            ],
            [
                'name' => 'Komunitas Pack',
                'events_count' => 3,
                'price' => 350000,
                'description' => 'Paket hemat untuk komunitas aktif. Hemat Rp 100.000 untuk 3 event kalender balap tahunan.',
                'is_active' => true,
            ],
            [
                'name' => 'Pro EO Pack',
                'events_count' => 10,
                'price' => 950000,
                'description' => 'Paket profesional untuk Event Organizer dan federasi balap sepeda skala besar. Tanpa batas masa aktif.',
                'is_active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            QuotaPackage::updateOrCreate(
                ['name' => $pkg['name']],
                $pkg
            );
        }
    }
}

