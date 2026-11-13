<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_route_renders_branded_404_page(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Keluar dari Sirkuit')
            ->assertSee('404')
            ->assertSee('Jelajahi Kalender Balap');
    }

    public function test_forbidden_route_renders_branded_403_page(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);

        $this->actingAs($user)
            ->get('/admin/events')
            ->assertForbidden()
            ->assertSee('Jalur Ini Dikhususkan')
            ->assertSee('403');
    }
}
