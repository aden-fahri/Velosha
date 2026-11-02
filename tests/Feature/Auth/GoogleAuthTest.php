<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function mockGoogleUser(array $attributes = [], array $raw = ['email_verified' => true]): SocialiteUser
    {
        $mock = Mockery::mock(SocialiteUser::class);
        $mock->user = $raw;
        $mock->shouldReceive('getId')->andReturn($attributes['id'] ?? 'google-123456');
        $mock->shouldReceive('getEmail')->andReturn($attributes['email'] ?? 'google_rider@velosha.id');
        $mock->shouldReceive('getName')->andReturn($attributes['name'] ?? 'Google Rider');
        $mock->shouldReceive('getNickname')->andReturn($attributes['nickname'] ?? 'rider');
        $mock->shouldReceive('getAvatar')->andReturn($attributes['avatar'] ?? 'https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($mock);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        return $mock;
    }

    public function test_google_redirect_falls_back_to_demo_when_credentials_empty_and_demo_active(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'app.demo_login' => true,
        ]);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect(route('auth.demo', ['provider' => 'google']));
    }

    public function test_google_redirect_returns_error_when_credentials_empty_and_demo_inactive(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'app.demo_login' => false,
        ]);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
    }

    public function test_google_callback_creates_new_user_and_authenticates(): void
    {
        $this->mockGoogleUser([
            'id' => 'google-123456',
            'email' => 'google_rider@velosha.id',
            'name' => 'Google Rider',
            'nickname' => 'rider',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'google_rider@velosha.id',
            'provider' => 'google',
            'provider_id' => 'google-123456',
            'role' => User::ROLE_PESERTA,
            'password' => null,
        ]);

        $created = User::where('email', 'google_rider@velosha.id')->first();
        $this->assertNotNull($created->email_verified_at);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_rejects_unverified_google_email(): void
    {
        $this->mockGoogleUser(['email' => 'unverified@example.com'], ['email_verified' => false]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
    }

    public function test_google_callback_links_existing_verified_peserta(): void
    {
        $existing = User::factory()->create([
            'email' => 'existing_rider@velosha.id',
            'role' => User::ROLE_PESERTA,
            'email_verified_at' => now(),
            'provider' => null,
            'provider_id' => null,
        ]);

        $this->mockGoogleUser([
            'id' => 'google-987654',
            'email' => 'existing_rider@velosha.id',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($existing);
        $this->assertDatabaseHas('users', [
            'id' => $existing->id,
            'email' => 'existing_rider@velosha.id',
            'provider' => 'google',
            'provider_id' => 'google-987654',
        ]);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_does_not_link_existing_privileged_roles(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_PENYELENGGARA, User::ROLE_OPERATOR] as $role) {
            $user = User::factory()->create([
                'email' => "priv_{$role}@velosha.id",
                'role' => $role,
                'email_verified_at' => now(),
                'provider' => null,
                'provider_id' => null,
            ]);

            $this->mockGoogleUser([
                'id' => "google-priv-{$role}",
                'email' => $user->email,
            ]);

            $response = $this->get(route('auth.google.callback'));

            $response->assertRedirect(route('login'));
            $response->assertSessionHasErrors([
                'email' => 'Akun ini dikelola dengan kata sandi. Masuk dengan email dan kata sandi.',
            ]);
            $this->assertGuest();
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'provider' => null,
                'provider_id' => null,
            ]);
        }
    }

    public function test_google_callback_does_not_link_existing_unverified_local_user(): void
    {
        $unverified = User::factory()->create([
            'email' => 'unverified_local@velosha.id',
            'role' => User::ROLE_PESERTA,
            'email_verified_at' => null,
            'provider' => null,
            'provider_id' => null,
        ]);

        $this->mockGoogleUser([
            'id' => 'google-unv-1',
            'email' => $unverified->email,
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'id' => $unverified->id,
            'provider' => null,
            'provider_id' => null,
        ]);
    }

    public function test_google_callback_handles_denied_access_without_exposing_exception(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andThrow(new \Exception('User cancelled Google OAuth consent'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        $this->assertStringNotContainsString('User cancelled Google OAuth consent', session('errors')->first('email'));
    }

    public function test_google_callback_handles_invalid_state_without_exposing_exception(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andThrow(new InvalidStateException('Invalid OAuth state signature'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        $this->assertStringNotContainsString('Invalid OAuth state signature', session('errors')->first('email'));
    }

    public function test_google_routes_are_throttled_after_limit(): void
    {
        config([
            'services.google.client_id' => 'client-id-test',
            'services.google.client_secret' => 'client-secret-test',
        ]);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        for ($i = 0; $i < 20; $i++) {
            $this->get(route('auth.google'));
        }

        $response = $this->get(route('auth.google'));
        $response->assertStatus(429);
    }

    public function test_google_user_with_null_password_fails_credential_login_cleanly(): void
    {
        $user = User::factory()->create([
            'email' => 'google_only@velosha.id',
            'password' => null,
            'role' => User::ROLE_PESERTA,
            'provider' => 'google',
            'provider_id' => 'google-test-123',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'some-random-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_user_can_visit_rejects_open_redirects(): void
    {
        $user = User::factory()->make(['role' => User::ROLE_PESERTA]);

        $this->assertFalse($user->canVisit('https://evil.example/events'));
        $this->assertFalse($user->canVisit('//evil.example'));
        $this->assertFalse($user->canVisit('javascript:alert(1)'));
        $this->assertTrue($user->canVisit('/events'));
        $this->assertTrue($user->canVisit('/dashboard'));
    }
}
