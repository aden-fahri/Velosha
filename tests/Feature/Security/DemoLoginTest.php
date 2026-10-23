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

    public function test_auth_shell_hides_demo_social_block_when_disabled(): void
    {
        config(['app.demo_login' => false]);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('atau lanjutkan dengan')
            ->assertDontSee('/auth/demo/google', false)
            ->assertDontSee('/auth/demo/facebook', false)
            ->assertDontSee('/auth/demo/tiktok', false);
    }

    public function test_auth_shell_shows_demo_social_block_when_enabled(): void
    {
        config(['app.demo_login' => true]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('atau lanjutkan dengan')
            ->assertSee('/auth/demo/google', false);
    }
}
