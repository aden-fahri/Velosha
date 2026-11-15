<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullApplicationSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'email_verified_at' => now(),
        ]);
    }

    public function test_guest_public_routes_return_no_server_errors(): void
    {
        $routes = [
            '/',
            '/events',
            '/about',
            '/rule',
            '/help',
            '/login',
            '/register',
            '/paket',
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $this->assertNotEquals(500, $response->getStatusCode(), "Route {$url} threw 500 for Guest!");
            $this->assertTrue(in_array($response->getStatusCode(), [200, 302], true), "Route {$url} unexpected status {$response->getStatusCode()} for Guest");
        }
    }

    public function test_peserta_routes_return_no_server_errors(): void
    {
        $peserta = $this->makeUser(User::ROLE_PESERTA);

        $routes = [
            '/',
            '/events',
            '/dashboard',
            '/dashboard/bantuan',
            '/dashboard/bantuan/buat',
            '/paket',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($peserta)->get($url);
            $this->assertNotEquals(500, $response->getStatusCode(), "Route {$url} threw 500 for Peserta!");
            $this->assertTrue(in_array($response->getStatusCode(), [200, 302], true), "Route {$url} unexpected status {$response->getStatusCode()} for Peserta");
        }
    }

    public function test_operator_routes_return_no_server_errors(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERATOR);

        $routes = [
            '/helpdesk',
            '/dashboard/bantuan',
            '/dashboard',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($operator)->get($url);
            $this->assertNotEquals(500, $response->getStatusCode(), "Route {$url} threw 500 for Operator!");
            $this->assertTrue(in_array($response->getStatusCode(), [200, 302], true), "Route {$url} unexpected status {$response->getStatusCode()} for Operator");
        }
    }

    public function test_penyelenggara_routes_return_no_server_errors(): void
    {
        $penyelenggara = $this->makeUser(User::ROLE_PENYELENGGARA);
        $org = Organization::create([
            'name' => 'Komunitas Gowes Nusantara',
            'slug' => 'komunitas-gowes-nusantara',
            'verification_status' => Organization::VERIFICATION_VERIFIED,
        ]);
        $org->users()->attach($penyelenggara->id, ['role' => 'owner']);

        $routes = [
            '/organisasi/dasbor',
            '/paket',
            '/organisasi/' . $org->slug,
            '/organisasi/' . $org->slug . '/events/create',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($penyelenggara)->get($url);
            $this->assertNotEquals(500, $response->getStatusCode(), "Route {$url} threw 500 for Penyelenggara!");
            $this->assertTrue(in_array($response->getStatusCode(), [200, 302], true), "Route {$url} unexpected status {$response->getStatusCode()} for Penyelenggara");
        }
    }

    public function test_admin_routes_return_no_server_errors(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $routes = [
            '/helpdesk/analitik',
            '/helpdesk',
            '/dashboard',
            '/paket',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($admin)->get($url);
            $this->assertNotEquals(500, $response->getStatusCode(), "Route {$url} threw 500 for Admin!");
            $this->assertTrue(in_array($response->getStatusCode(), [200, 302], true), "Route {$url} unexpected status {$response->getStatusCode()} for Admin");
        }
    }
}
