<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageDomTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_hero_section_has_correct_mobile_desktop_elements(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // 1. Title (Siap gowes sampai finis?)
        $response->assertSee('Siap gowes');
        $response->assertSee('sampai');
        $response->assertSee('finis?');

        // 2. Rider visual image (rider_final.png)
        $response->assertSee('rider_final.png');

        // 3. CTA button with wire:navigate.hover pointing to events
        $response->assertSee('Cari event');
        $response->assertSee('wire:navigate.hover', false);

        // 4. Ticket slider component
        $response->assertSee('hero-ticket-col');
        $response->assertSee('GRAN FONDO');
    }

    public function test_layout_contains_spa_progress_and_anti_double_click_guard(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // Top progress bar
        $response->assertSee('velo-nav-progress');

        // Form submit guard & icon observer
        $response->assertSee('__veloFormSubmitGuard', false);
        $response->assertSee('__veloRenderIcons', false);
        $response->assertSee('__veloResetFormButtons', false);
    }

    public function test_home_bento_links_have_wire_navigate_hover(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // Internal bento links
        $response->assertSee('href="/help" wire:navigate.hover', false);
        $response->assertSee('href="/about" wire:navigate.hover', false);
        $response->assertSee('href="/rule" wire:navigate.hover', false);
        $response->assertSee('href="/paket" wire:navigate.hover', false);
    }
}
