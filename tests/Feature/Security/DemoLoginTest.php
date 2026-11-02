<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_login_404_when_disabled(): void
    {
        config(['app.demo_login' => false]);
        $this->get('/auth/demo/google')->assertStatus(404);
    }

    public function test_auth_shell_hides_google_button_when_demo_off_and_no_credentials(): void
    {
        config([
            'app.demo_login' => false,
            'services.google.client_id' => null,
        ]);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('atau lanjutkan dengan')
            ->assertDontSee('/auth/google', false);
    }

    public function test_auth_shell_shows_google_button_and_no_demo_link_when_demo_off_and_credentials_set(): void
    {
        config([
            'app.demo_login' => false,
            'services.google.client_id' => 'google-client-id-test',
        ]);

        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('atau lanjutkan dengan');
        $response->assertSee('/auth/google', false);
        $response->assertDontSee('/auth/demo/', false);
    }

    public function test_auth_shell_shows_google_button_and_redirects_to_demo_when_demo_on_and_no_credentials(): void
    {
        config([
            'app.demo_login' => true,
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('atau lanjutkan dengan')
            ->assertSee('/auth/google', false);

        $this->get('/auth/google')
            ->assertRedirect(route('auth.demo', ['provider' => 'google']));
    }
}
