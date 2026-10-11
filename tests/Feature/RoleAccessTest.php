<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_main_login_from_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_legacy_panel_login_url_redirects_to_main_login(): void
    {
        $this->get('/admin/login')->assertRedirect(route('login'));
    }

    public function test_guest_with_intended_admin_url_lands_on_panel_after_admin_login(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->from('/admin')
            ->post('/login', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $response->assertRedirect('/admin');
    }

    public function test_peserta_with_intended_admin_url_falls_back_to_dashboard(): void
    {
        $user = $this->makeUser(User::ROLE_PESERTA);

        $response = $this->withSession(['url.intended' => url('/admin')])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_admin_with_intended_dashboard_url_falls_back_to_panel(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->withSession(['url.intended' => url('/dashboard')])
            ->post('/login', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $response->assertRedirect('/admin');
    }

    public function test_panel_logout_redirects_to_home(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->actingAs($admin)->post(route('filament.admin.auth.logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_demo_social_login_is_guest_only(): void
    {
        $user = $this->makeUser(User::ROLE_PESERTA);

        $response = $this->actingAs($user)->get('/auth/demo/google');

        $response->assertRedirect('/dashboard');
        $this->assertSame($user->id, Auth::id());
    }

    public function test_peserta_can_access_dashboard(): void
    {
        $user = $this->makeUser(User::ROLE_PESERTA);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_peserta_is_forbidden_from_admin_panel(): void
    {
        $user = $this->makeUser(User::ROLE_PESERTA);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_forbidden_page_shows_friendly_indonesian_view(): void
    {
        $user = $this->makeUser(User::ROLE_PESERTA);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden()
            ->assertSee('Akses Jalur Khusus')
            ->assertSee('Ke Dashboard Saya');
    }

    public function test_panel_renders_flash_error_banner(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->withSession(['error' => 'Hanya untuk peran peserta — silakan keluar dan ganti akun.'])
            ->get('/admin')
            ->assertOk()
            ->assertSee('Hanya untuk peran peserta')
            ->assertSee('Tutup');
    }

    public function test_admin_is_redirected_home_when_accessing_peserta_dashboard(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin');
    }

    public function test_penyelenggara_can_access_admin_panel(): void
    {
        $user = $this->makeUser(User::ROLE_PENYELENGGARA);

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_login_redirects_peserta_to_dashboard(): void
    {
        $user = $this->makeUser(User::ROLE_PESERTA);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_login_redirects_admin_to_panel(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_registration_always_assigns_peserta_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Penyusup Role',
            'email' => 'penyusup@example.com',
            'phone' => '081111111111',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'penyusup@example.com',
            'role' => User::ROLE_PESERTA,
        ]);
    }

    public function test_demo_social_login_creates_and_authenticates_user(): void
    {
        $response = $this->get('/auth/demo/google');

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'google.demo@velosha.id',
            'provider' => 'google',
            'provider_id' => 'demo-google-001',
            'role' => User::ROLE_PESERTA,
        ]);
    }

    public function test_demo_social_login_is_idempotent(): void
    {
        $this->get('/auth/demo/tiktok');
        $this->post('/logout');
        $this->get('/auth/demo/tiktok');

        $this->assertAuthenticated();
        $this->assertSame(1, User::where('email', 'tiktok.demo@velosha.id')->count());
    }

    public function test_demo_social_rejects_unknown_provider(): void
    {
        $this->get('/auth/demo/twitter')->assertNotFound();
    }

    public function test_admin_bounced_back_to_origin_page_with_flash_when_opening_checkout(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $origin = url('/events/velosha-gran-fondo-borobudur-2026');

        $response = $this->actingAs($admin)->get('/checkout', [
            'HTTP_REFERER' => $origin,
        ]);

        $response->assertRedirect($origin);
        $response->assertSessionHas('error');
    }

    public function test_admin_redirected_home_when_referer_is_unsafe(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->get('/checkout', ['HTTP_REFERER' => url('/login')])
            ->assertRedirect('/admin');

        $this->actingAs($admin)
            ->get('/checkout', ['HTTP_REFERER' => url('/admin')])
            ->assertRedirect('/admin');

        $this->actingAs($admin)
            ->get('/checkout', ['HTTP_REFERER' => 'http://evil.example.com/phish'])
            ->assertRedirect('/admin');
    }

    public function test_admin_with_intended_checkout_url_lands_on_panel_after_login(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->withSession(['url.intended' => url('/checkout')])
            ->post('/login', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $response->assertRedirect('/admin');
    }

    public function test_peserta_with_intended_checkout_url_lands_on_checkout(): void
    {
        $peserta = $this->makeUser(User::ROLE_PESERTA);

        $response = $this->withSession(['url.intended' => url('/checkout')])
            ->post('/login', [
                'email' => $peserta->email,
                'password' => 'password',
            ]);

        $response->assertRedirect('/checkout');
    }
}
