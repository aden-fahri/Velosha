<?php

namespace Tests\Feature\Organization;

use App\Models\Event;
use App\Models\Organization;
use App\Models\QuotaPackage;
use App\Models\QuotaPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_peserta_registers_org_buys_quota_becomes_penyelenggara_and_creates_event(): void
    {
        // 1. Peserta mendaftar / login
        $user = User::factory()->create([
            'role' => User::ROLE_PESERTA,
        ]);

        $this->actingAs($user);
        $this->assertTrue($user->isPeserta());
        $this->assertFalse($user->isPenyelenggara());

        // 2. Peserta mendaftarkan komunitas/organisasinya
        $postResponse = $this->post(route('organizations.store'), [
            'name' => 'Nusantara Cycling Community',
            'description' => 'Komunitas sepeda road race & gran fondo Indonesia',
        ]);

        $postResponse->assertRedirect(route('organizations.verification', ['organization' => 'nusantara-cycling-community']));

        $org = Organization::where('slug', 'nusantara-cycling-community')->first();
        $this->assertNotNull($org);
        $this->assertSame(0, $org->quotaBalance());

        // Verifikasi organisasi agar siap menayangkan event
        $org->update(['verification_status' => Organization::VERIFICATION_VERIFIED]);

        // 3. User mengakses Dasbor Penyelenggara
        $hubResponse = $this->get(route('organizations.show', $org));
        $hubResponse->assertOk();
        $hubResponse->assertSee('Nusantara Cycling Community');
        $hubResponse->assertSee('0'); // Sisa kuota 0

        // 4. User membuka halaman buat event saat kuota masih 0
        $package = QuotaPackage::create([
            'name' => 'Komunitas Pack',
            'events_count' => 3,
            'price' => 350000,
            'description' => '3 Kuota Event Selamanya',
            'is_active' => true,
        ]);

        $createEventResponse = $this->get(route('organizations.events.create', $org));
        $createEventResponse->assertOk();
        $createEventResponse->assertSee('Pilih Paket Kuota');

        // 5. Peserta membeli paket kuota (simulasi lokal saat Midtrans key belum aktif)
        $purchaseResponse = $this->post(route('organizations.quota.purchase', $org), [
            'package_id' => $package->id,
            'redirect_to' => 'events.create',
        ]);

        $purchaseResponse->assertRedirect(route('organizations.events.create', $org));
        $this->assertSame(3, $org->quotaBalance());

        // Memastikan role peserta otomatis ter-upgrade menjadi 'penyelenggara'
        $user->refresh();
        $this->assertSame(User::ROLE_PENYELENGGARA, $user->role);
        $this->assertTrue($user->isPenyelenggara());

        // 6. User yang kini penyelenggara mengisi form dan membuat event baru
        $storeEventResponse = $this->post(route('organizations.events.store', $org), [
            'title' => 'Bromo Gran Fondo 2026',
            'type' => 'gran_fondo',
            'event_date' => now()->addMonths(2)->format('Y-m-d'),
            'start_time' => '06:00',
            'location_name' => 'Tosari, Pasuruan, Jawa Timur',
            'description' => 'Balap sepeda menanjak ikonik dengan pemandangan kaldera Bromo.',
            'category_name' => 'Gran Fondo 130 KM',
            'category_price' => 350000,
            'category_quota' => 600,
            'category_distance' => 130,
            'category_cut_off' => 6.5,
        ]);

        $storeEventResponse->assertRedirect(route('organizations.show', ['organization' => $org->slug, 'tab' => 'events']));
        $this->assertDatabaseHas('events', [
            'organization_id' => $org->id,
            'title' => 'Bromo Gran Fondo 2026',
            'status' => Event::STATUS_PUBLISHED,
        ]);

        // Kuota terpotong 1 karena event published (3 - 1 = 2)
        $this->assertSame(2, $org->quotaBalance());
    }
}

