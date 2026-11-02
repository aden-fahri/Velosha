<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

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
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-123456');
        $socialiteUser->shouldReceive('getEmail')->andReturn('google_rider@velosha.id');
        $socialiteUser->shouldReceive('getName')->andReturn('Google Rider');
        $socialiteUser->shouldReceive('getNickname')->andReturn('rider');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'google_rider@velosha.id',
            'provider' => 'google',
            'provider_id' => 'google-123456',
            'role' => User::ROLE_PESERTA,
        ]);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_links_existing_user_by_email(): void
    {
        $existing = User::factory()->create([
            'email' => 'existing_rider@velosha.id',
            'provider' => null,
            'provider_id' => null,
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-987654');
        $socialiteUser->shouldReceive('getEmail')->andReturn('existing_rider@velosha.id');
        $socialiteUser->shouldReceive('getName')->andReturn('Existing Rider');
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar2.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

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
}
