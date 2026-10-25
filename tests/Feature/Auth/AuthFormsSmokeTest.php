<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class AuthFormsSmokeTest extends TestCase
{
    /**
     * Memastikan halaman login ter-render 200 OK beserta seluruh elemen form pentingnya.
     */
    public function test_login_page_renders_ok_with_form_elements(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('<form', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('type="submit"', false);
    }

    /**
     * Memastikan halaman registrasi ter-render 200 OK beserta seluruh elemen form pentingnya.
     */
    public function test_register_page_renders_ok_with_form_elements(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('<form', false);
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);
        $response->assertSee('type="submit"', false);
    }
}
