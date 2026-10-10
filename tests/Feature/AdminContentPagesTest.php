<?php

namespace Tests\Feature;

use App\Models\AboutContent;
use App\Models\HelpContent;
use App\Models\RuleContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_admin_can_open_the_three_content_list_pages(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/admin/about-contents')->assertOk();
        $this->actingAs($admin)->get('/admin/rule-contents')->assertOk();
        $this->actingAs($admin)->get('/admin/help-contents')->assertOk();
    }

    public function test_admin_can_open_content_edit_forms_prefilled_with_defaults(): void
    {
        $admin = $this->makeAdmin();
        $about = AboutContent::create(AboutContent::defaults());
        $rule = RuleContent::create(RuleContent::defaults());
        $help = HelpContent::create(HelpContent::defaults());

        $this->actingAs($admin)->get('/admin/about-contents/'.$about->id.'/edit')
            ->assertOk()
            ->assertSee('Kami Menghidupkan Budaya Sepeda di Sini');

        $this->actingAs($admin)->get('/admin/rule-contents/'.$rule->id.'/edit')
            ->assertOk()
            ->assertSee('Kelengkapan Wajib');

        $this->actingAs($admin)->get('/admin/help-contents/'.$help->id.'/edit')
            ->assertOk()
            ->assertSee('Registrasi &amp; Akun', false);
    }

    public function test_content_is_seeded_as_single_row_via_first_or_create(): void
    {
        AboutContent::create(AboutContent::defaults());
        AboutContent::query()->firstOrCreate([], AboutContent::defaults());

        $this->assertSame(1, AboutContent::query()->count());
    }

    public function test_non_admin_cannot_access_content_resources(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->actingAs($peserta)->get('/admin/about-contents')->assertForbidden();
    }

    public function test_admin_navigation_contains_content_menu_items(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('About Page');
        $response->assertSee('Rules Page');
        $response->assertSee('Help Page');
    }
}
