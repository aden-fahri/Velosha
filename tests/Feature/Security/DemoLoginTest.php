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
}