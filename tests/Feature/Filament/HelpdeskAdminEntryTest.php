<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\HelpdeskOverview;
use App\Models\HelpdeskTicket;
use App\Models\SatisfactionRating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HelpdeskAdminEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_restriction_enforces_admin_only(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->actingAs($admin);
        $this->assertTrue(HelpdeskOverview::canView(), 'Admin must be allowed to view HelpdeskOverview');

        $this->actingAs($penyelenggara);
        $this->assertFalse(HelpdeskOverview::canView(), 'Penyelenggara must not be allowed to view HelpdeskOverview');

        $this->actingAs($operator);
        $this->assertFalse(HelpdeskOverview::canView(), 'Operator must not be allowed to view HelpdeskOverview');

        $this->actingAs($peserta);
        $this->assertFalse(HelpdeskOverview::canView(), 'Peserta must not be allowed to view HelpdeskOverview');

        auth()->logout();
        $this->assertFalse(HelpdeskOverview::canView(), 'Guest must not be allowed to view HelpdeskOverview');
    }

    public function test_widget_displays_exact_metrics_with_fixture_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR, 'name' => 'Support Op']);

        // 3 Open tickets
        HelpdeskTicket::factory()->create(['reporter_id' => $peserta->id, 'status' => 'Open']);
        HelpdeskTicket::factory()->create(['reporter_id' => $peserta->id, 'status' => 'Open']);
        HelpdeskTicket::factory()->create(['reporter_id' => $peserta->id, 'status' => 'Open']);

        // 2 In-Progress tickets
        HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'status' => 'In-Progress',
            'created_at' => Carbon::parse('2026-10-26 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-26 10:10:00'), // 10m
        ]);

        $tClosed = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'created_at' => Carbon::parse('2026-10-26 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-26 10:20:00'), // 20m -> avg (10+20)/2 = 15m
        ]);

        HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'status' => 'In-Progress',
        ]);

        // Ratings for overall average: 5 and 4 -> avg 4.5
        SatisfactionRating::create(['ticket_id' => $tClosed->id, 'score' => 5, 'created_at' => now()]);
        
        $tClosed2 = HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operator->id,
            'status' => 'Closed',
            'first_response_at' => null,
            'closed_at' => now(),
        ]);
        SatisfactionRating::create(['ticket_id' => $tClosed2->id, 'score' => 4, 'created_at' => now()]);

        $component = Livewire::actingAs($admin)->test(HelpdeskOverview::class);

        $component->assertSuccessful()
            ->assertSee('Tiket Terbuka')
            ->assertSee('3')
            ->assertSee('Sedang Diproses')
            ->assertSee('2')
            ->assertSee('Rata-rata Respons')
            ->assertSee('15m')
            ->assertSee('Rating Kepuasan')
            ->assertSee('4.5')
            ->assertSee('/helpdesk/analitik');
    }

    public function test_widget_displays_dash_without_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Livewire::actingAs($admin)->test(HelpdeskOverview::class)
            ->assertSuccessful()
            ->assertSee('Tiket Terbuka')
            ->assertSee('0')
            ->assertSee('Sedang Diproses')
            ->assertSee('Rata-rata Respons')
            ->assertSee('-')
            ->assertSee('Rating Kepuasan');
    }

    public function test_admin_panel_navigation_contains_helpdesk_group_for_admin_only(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        // 1. Admin sees navigation group and items
        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Helpdesk')
            ->assertSee('Antrian tiket')
            ->assertSee('/helpdesk')
            ->assertSee('Analitik helpdesk')
            ->assertSee('/helpdesk/analitik');

        // 2. Penyelenggara does NOT see helpdesk navigation items
        $this->actingAs($penyelenggara)
            ->get('/admin')
            ->assertOk()
            ->assertDontSee('/helpdesk/analitik')
            ->assertDontSee('Analitik helpdesk');
    }
}
