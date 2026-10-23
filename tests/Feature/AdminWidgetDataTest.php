<?php

namespace Tests\Feature;

use App\Filament\Widgets\RecentRegistrationsWidget;
use App\Filament\Widgets\RegistrationStatusChart;
use App\Filament\Widgets\RegistrationTrendChart;
use App\Filament\Widgets\UpcomingEventsWidget;
use App\Filament\Widgets\VeloshaStatsOverview;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\User;
use DateTimeInterface;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

class AdminWidgetDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Jalankan metode proteksi widget (getData/getStats) langsung —
     * widget Filament lazy, jadi metode ini tidak pernah tereksekusi
     * pada test HTTP biasa.
     */
    private function invokeData(object $widget, string $method): array
    {
        return (new ReflectionMethod($widget, $method))->invoke($widget);
    }

    private function makeCategory(): EventCategory
    {
        $event = Event::factory()->create();

        return EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Gran Fondo',
            'code' => 'GF',
            'distance_km' => 130,
            'elevation_gain_m' => 1450,
            'price' => 500000,
            'quota' => 10,
            'registered_count' => 0,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 7,
        ]);
    }

    private function makeRegistration(User $user, EventCategory $category, string $status = Registration::STATUS_PENDING): Registration
    {
        return Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Arif Rahman Wibowo',
            'identity_number' => '330801240898000'.random_int(0, 999),
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Siti Handayani',
            'emergency_contact_relation' => 'Istri / Suami',
            'emergency_contact_phone' => '081987654321',
            'medical_notes' => null,
            'club' => 'Menoreh Peloton CC',
            'status' => $status,
        ]);
    }

    private function backdate(Registration $registration, DateTimeInterface $tanggal): void
    {
        Registration::query()->whereKey($registration->id)->update(['created_at' => $tanggal]);
    }

    public function test_trend_chart_returns_six_months_of_integer_counts(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->makeRegistration($peserta, $this->makeCategory());

        $bulanLalu = $this->makeRegistration($peserta, $this->makeCategory());
        $this->backdate($bulanLalu, now()->subMonth()->startOfMonth());

        $diLuarJendela = $this->makeRegistration($peserta, $this->makeCategory());
        $this->backdate($diLuarJendela, now()->subMonths(7));

        $data = $this->invokeData(new RegistrationTrendChart, 'getData');

        $this->assertCount(6, $data['labels']);

        $counts = $data['datasets'][0]['data'];
        $this->assertCount(6, $counts);
        array_walk($counts, fn ($count) => $this->assertIsInt($count));

        $this->assertSame(1, $counts[5]);
        $this->assertSame(1, $counts[4]);
        $this->assertSame(2, array_sum($counts));
    }

    public function test_trend_chart_handles_empty_database(): void
    {
        $data = $this->invokeData(new RegistrationTrendChart, 'getData');

        $this->assertCount(6, $data['labels']);
        $this->assertSame([0, 0, 0, 0, 0, 0], $data['datasets'][0]['data']);
    }

    public function test_status_chart_returns_four_status_counts(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->makeRegistration($peserta, $this->makeCategory(), Registration::STATUS_PENDING);
        $this->makeRegistration($peserta, $this->makeCategory(), Registration::STATUS_PAID);
        $this->makeRegistration($peserta, $this->makeCategory(), Registration::STATUS_PAID);
        $this->makeRegistration($peserta, $this->makeCategory(), Registration::STATUS_CHECKED_IN);
        $this->makeRegistration($peserta, $this->makeCategory(), Registration::STATUS_CANCELLED);

        $data = $this->invokeData(new RegistrationStatusChart, 'getData');

        $this->assertSame(['Lunas', 'Check-in RPC', 'Menunggu Bayar', 'Dibatalkan'], $data['labels']);
        $this->assertSame([2, 1, 1, 1], $data['datasets'][0]['data']);
    }

    public function test_stats_overview_returns_seven_stats(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $kategori = $this->makeCategory();
        $this->makeRegistration($peserta, $kategori, Registration::STATUS_PAID);

        $stats = $this->invokeData(new VeloshaStatsOverview, 'getStats');

        $this->assertCount(7, $stats);
        array_walk($stats, fn ($stat) => $this->assertInstanceOf(Stat::class, $stat));
    }

    public function test_upcoming_events_widget_renders_table(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $event = Event::factory()->create(['title' => 'Borobudur Gran Fondo']);
        EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Medio Fondo',
            'code' => 'MF',
            'distance_km' => 90,
            'elevation_gain_m' => 900,
            'price' => 350000,
            'quota' => 20,
            'registered_count' => 0,
            'wave_start_time' => 'Wave 1 - 05:45 WIB',
            'cut_off_hours' => 6,
        ]);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertSuccessful()
            ->assertSee($event->title);
    }

    public function test_recent_registrations_widget_renders_live_list(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $kategori = $this->makeCategory();
        $this->makeRegistration($peserta, $kategori, Registration::STATUS_PENDING);

        Livewire::test(RecentRegistrationsWidget::class)
            ->assertSuccessful()
            ->assertSee('Pendaftar Terbaru')
            ->assertSee('Arif Rahman Wibowo');
    }
}
