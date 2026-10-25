<?php

namespace Tests\Feature\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\SatisfactionRating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAnalyticsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_analytics(): void
    {
        $this->get('/helpdesk/analitik')
            ->assertRedirect('/login');
    }

    public function test_peserta_accessing_analytics_is_bounced_with_error_flash(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $response = $this->actingAs($peserta)->get('/helpdesk/analitik');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_operator_accessing_analytics_is_bounced_with_error_flash(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        $response = $this->actingAs($operator)->get('/helpdesk/analitik');

        $response->assertRedirect('/helpdesk');
        $response->assertSessionHas('error');
    }

    public function test_admin_can_access_helpdesk_analytics_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Kurnia Operator',
            'is_system' => false,
        ]);

        $ticket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:15:00'), // 15m
        ]);

        SatisfactionRating::create([
            'ticket_id' => $ticket->id,
            'score' => 5,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/helpdesk/analitik');

        $response->assertOk();
        $response->assertSee('Analitik & Performa Helpdesk');
        $response->assertSee('Volume Tiket Helpdesk');
        $response->assertSee('Rata-Rata Waktu Respons per Operator');
        $response->assertSee('Kepuasan Pelanggan (Keseluruhan)');
        $response->assertSee('Peringkat Operator Terbaik (Top Operators)');
        $response->assertSee('Kurnia Operator');
        $response->assertSee('15m');
    }

    public function test_admin_sidebar_contains_analitik_link_and_operator_sidebar_does_not(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        // Admin sidebar on /helpdesk contains Analitik Helpdesk link
        $this->actingAs($admin)
            ->get('/helpdesk')
            ->assertOk()
            ->assertSee('/helpdesk/analitik')
            ->assertSee('Analitik Helpdesk');

        // Operator sidebar on /helpdesk does NOT contain Analitik Helpdesk link
        $this->actingAs($operator)
            ->get('/helpdesk')
            ->assertOk()
            ->assertDontSee('/helpdesk/analitik')
            ->assertDontSee('Analitik Helpdesk');
    }
}
