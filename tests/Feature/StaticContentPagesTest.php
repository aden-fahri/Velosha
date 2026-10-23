<?php

namespace Tests\Feature;

use App\Models\AboutContent;
use App\Models\Event;
use App\Models\HelpContent;
use App\Models\RuleContent;
use App\Support\EventCardPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticContentPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_with_default_content_when_table_empty(): void
    {
        $response = $this->get('/about');

        $response->assertOk();
        $response->assertSee('Tentang Velosha');
        $response->assertSee('Platform Registrasi Balap Sepeda Indonesia');
        $response->assertSee('Nilai Utama');
        $response->assertSee('Siap mulai musim 2026?');
        $response->assertDontSee('Sirkuit Resmi');
        $response->assertDontSee('Termasuk Paket');
    }

    public function test_rule_page_renders_with_default_content_when_table_empty(): void
    {
        $response = $this->get('/rule');

        $response->assertOk();
        $response->assertSee('Aturan balap & kode etik', false);
        $response->assertSee('Kendaraan');
        $response->assertSee('Sanksi & diskualifikasi', false);
        $response->assertSee('Seluruh keputusan Race Comisaris');
    }

    public function test_help_page_renders_with_default_content_when_table_empty(): void
    {
        $response = $this->get('/help');

        $response->assertOk();
        $response->assertSee('Pusat bantuan');
        $response->assertSee('WhatsApp');
        $response->assertSee('Bagaimana cara mendaftar?');
        $response->assertSee('Istilah Sepeda');
        $response->assertSee('Apa itu Cut-Off Time (COT)?');
        $response->assertSee('Masih ada yang mengganjal?');
    }

    public function test_about_page_uses_content_from_database(): void
    {
        $data = AboutContent::defaults();
        $data['manifesto_title'] = 'Judul Manifesto Dari Database';
        $data['cta_title'] = 'CTA Khusus Dari Database';
        AboutContent::create($data);

        $response = $this->get('/about');

        $response->assertOk();
        $response->assertSee('Judul Manifesto Dari Database');
        $response->assertSee('CTA Khusus Dari Database');
        $response->assertDontSee('Kami Menghidupkan Budaya Sepeda di Sini');
    }

    public function test_rule_page_renders_ampersand_as_single_escaped_entity(): void
    {
        $response = $this->get('/rule');

        $response->assertOk();
        $response->assertSee('Aturan Balap & Kode Etik');
        $response->assertSee('Kendaraan & Peralatan');
        $response->assertDontSee('&amp;amp;', false);
    }

    public function test_rule_page_uses_content_from_database(): void
    {
        $data = RuleContent::defaults();
        $data['groups'][0]['title'] = 'Kelompok Aturan Dari Database';
        $data['sanksi_note'] = 'Catatan sanksi versi database.';
        RuleContent::create($data);

        $response = $this->get('/rule');

        $response->assertOk();
        $response->assertSee('Kelompok Aturan Dari Database');
        $response->assertSee('Catatan sanksi versi database.');
        $response->assertDontSee('Seluruh keputusan Race Comisaris');
    }

    public function test_help_page_uses_content_from_database(): void
    {
        $data = HelpContent::defaults();
        $data['faq_groups'][0]['items'][0]['q'] = 'Pertanyaan FAQ dari database?';
        $data['contacts'][0]['value'] = '+62 811 111 2222';
        HelpContent::create($data);

        $response = $this->get('/help');

        $response->assertOk();
        $response->assertSee('Pertanyaan FAQ dari database?');
        $response->assertSee('+62 811 111 2222');
        $response->assertDontSee('Bagaimana cara mendaftar?');
    }

    public function test_event_photo_urls_use_asset_instead_of_storage_disk_url(): void
    {
        config(['filesystems.disks.public.url' => 'http://other-host.test/storage']);

        $photo = (object) ['photo_path' => 'event-photos/uji-coba.jpg'];

        $url = EventCardPresenter::photoUrl($photo);

        $this->assertSame(asset('storage/event-photos/uji-coba.jpg'), $url);
        $this->assertStringNotContainsString('other-host.test', $url);
    }

    public function test_event_detail_gpx_link_uses_asset_path(): void
    {
        $event = Event::factory()->create(['gpx_file_path' => 'gpx/rute-uji.gpx']);

        $response = $this->get('/events/'.$event->slug);

        $response->assertOk();
        $response->assertSee(asset('storage/gpx/rute-uji.gpx'), false);
        $this->assertStringNotContainsString('other-host.test', $response->getContent());
    }

    public function test_event_detail_shows_uploaded_map_image_instead_of_iframe(): void
    {
        $event = Event::factory()->create(['map_image_path' => 'event-maps/peta-rute.jpg']);

        $response = $this->get('/events/'.$event->slug);

        $response->assertOk();
        $response->assertSee(asset('storage/event-maps/peta-rute.jpg'), false);
        $this->assertStringNotContainsString('maps.google.com/maps', $response->getContent());
        $response->assertDontSee('Profil Elevasi Tanjakan');
    }

    public function test_event_detail_falls_back_to_placeholder_when_no_map_image(): void
    {
        $event = Event::factory()->create(['map_image_path' => null]);

        $response = $this->get('/events/'.$event->slug);

        $response->assertOk();
        $response->assertSee('Peta rute belum tersedia');
        $this->assertStringNotContainsString('maps.google.com', $response->getContent());
        $this->assertStringNotContainsString('<iframe', $response->getContent());
    }

    public function test_filament_uploads_default_to_public_disk(): void
    {
        $this->assertSame('public', config('filament.default_filesystem_disk'));
    }

    public function test_home_hero_uses_the_redesigned_illustration(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Siap gowes');
        $response->assertSee('/images/hero/rider_final.png', false);
        $response->assertSee('Cari event');
    }
}
