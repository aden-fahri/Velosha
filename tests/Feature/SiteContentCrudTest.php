<?php

namespace Tests\Feature;

use App\Models\FooterContent;
use App\Models\RaceBannerContent;
use App\Models\TicketPackageContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteContentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_default_ticket_package_when_table_empty(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Isi Paket Tiket atau benefit')
            ->assertSee('Yang Kamu Dapat dari Satu Tiket')
            ->assertSee('Medali Finisher Logam')
            ->assertSee('Benefit Lain dari Satu Tiket');
    }

    public function test_home_shows_saved_ticket_package_content(): void
    {
        TicketPackageContent::create([
            'eyebrow' => 'Paket Khusus',
            'title' => 'Judul Paket Custom',
            'description' => 'Deskripsi custom isi paket.',
            'features' => [
                ['icon' => 'star', 'title' => 'Fitur Custom', 'desc' => 'Deskripsi fitur custom.'],
            ],
            'benefits_title' => 'Benefit Custom',
            'benefits' => [
                ['text' => 'Benefit khusus nomor satu'],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Judul Paket Custom')
            ->assertSee('Fitur Custom')
            ->assertSee('Benefit khusus nomor satu')
            ->assertDontSee('Yang Kamu Dapat dari Satu Tiket');
    }

    public function test_home_shows_default_footer_when_table_empty(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Registrasi maraton, gran fondo, dan endurance ride di Indonesia.')
            ->assertSee('KOM Climb')
            ->assertSee('Portal Peserta')
            ->assertSee('Virtual Account')
            ->assertSee('Seluruh hak cipta dilindungi.');
    }

    public function test_home_shows_saved_footer_content(): void
    {
        FooterContent::create([
            'brand_description' => 'Deskripsi brand footer custom.',
            'kategori_title' => 'Kategori Custom',
            'kategori_links' => [
                ['label' => 'Tautan Kategori Custom', 'url' => '/events'],
            ],
            'bantuan_title' => 'Bantuan Custom',
            'bantuan_links' => [
                ['label' => 'Pusat Bantuan', 'url' => '/help'],
            ],
            'payment_title' => 'Bayar Custom',
            'payment_methods' => [
                ['label' => 'PayPal'],
            ],
            'copyright' => 'Hak cipta footer custom 2099.',
            'bottom_links' => [
                ['label' => 'Tentang Kami', 'url' => '/about'],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Deskripsi brand footer custom.')
            ->assertSee('Tautan Kategori Custom')
            ->assertSee('Pusat Bantuan')
            ->assertSee('PayPal')
            ->assertSee('Hak cipta footer custom 2099.')
            ->assertSee('Tentang Kami')
            ->assertDontSee('Registrasi maraton, gran fondo, dan endurance ride di Indonesia.');
    }

    public function test_admin_can_open_ticket_package_and_footer_resources(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $paket = TicketPackageContent::create(TicketPackageContent::defaults());
        $footer = FooterContent::create(FooterContent::defaults());

        $this->actingAs($admin)->get('/admin/ticket-package-contents')->assertOk();
        $this->actingAs($admin)->get('/admin/ticket-package-contents/'.$paket->id.'/edit')->assertOk();
        $this->actingAs($admin)->get('/admin/footer-contents')->assertOk();
        $this->actingAs($admin)->get('/admin/footer-contents/'.$footer->id.'/edit')->assertOk();
    }

    public function test_admin_can_open_create_pages_when_tables_empty(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/ticket-package-contents/create')->assertOk();
        $this->actingAs($admin)->get('/admin/footer-contents/create')->assertOk();
    }

    public function test_peserta_cannot_open_content_resources(): void
    {
        $peserta = User::factory()->create(['role' => 'peserta']);
        TicketPackageContent::create(TicketPackageContent::defaults());
        FooterContent::create(FooterContent::defaults());

        $this->actingAs($peserta)->get('/admin/ticket-package-contents')->assertForbidden();
        $this->actingAs($peserta)->get('/admin/footer-contents')->assertForbidden();
    }

    public function test_home_shows_admin_race_banner_image(): void
    {
        Storage::disk('public')->put('race-banners/banner-utama.jpg', 'placeholder-bytes');
        RaceBannerContent::create(['image_path' => 'race-banners/banner-utama.jpg']);

        $this->get('/')
            ->assertOk()
            ->assertSee('storage/race-banners/banner-utama.jpg', false);
    }

    public function test_home_keeps_race_banner_slot_without_admin_image(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('h-44 sm:h-48 mb-10', false);
    }

    public function test_admin_can_open_race_banner_resource(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $row = RaceBannerContent::create(['image_path' => 'race-banners/x.jpg']);

        $this->actingAs($admin)->get('/admin/race-banner-contents')->assertOk();
        $this->actingAs($admin)->get('/admin/race-banner-contents/'.$row->id.'/edit')->assertOk();
    }

    public function test_admin_can_open_race_banner_create_page_when_table_empty(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/race-banner-contents/create')->assertOk();
    }

    public function test_peserta_cannot_open_race_banner_resource(): void
    {
        $peserta = User::factory()->create(['role' => 'peserta']);
        RaceBannerContent::create(['image_path' => 'race-banners/x.jpg']);

        $this->actingAs($peserta)->get('/admin/race-banner-contents')->assertForbidden();
    }
}
