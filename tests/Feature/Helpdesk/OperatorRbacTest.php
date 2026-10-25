<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OperatorRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_enum_contains_operator(): void
    {
        $this->assertSame('operator', UserRole::OPERATOR->value);
        $this->assertArrayHasKey('operator', UserRole::options());
        $this->assertContains('operator', User::ROLES);
    }

    public function test_user_model_role_helpers_and_home_route(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        $this->assertTrue($operator->isOperator());
        $this->assertFalse($operator->isAdmin());
        $this->assertFalse($operator->isPeserta());
        $this->assertFalse($operator->isPenyelenggara());

        $this->assertSame('/helpdesk', $operator->home());
        $this->assertSame('/dashboard', $peserta->home());
        $this->assertSame('/admin', $admin->home());
        $this->assertSame('/admin', $penyelenggara->home());
    }

    public function test_filament_panel_access_allows_admin_and_penyelenggara_but_denies_operator_and_peserta(): void
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertTrue($penyelenggara->canAccessPanel($panel));
        $this->assertFalse($operator->canAccessPanel($panel));
        $this->assertFalse($peserta->canAccessPanel($panel));
    }

    public function test_operator_and_admin_can_access_helpdesk_queue_placeholder(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($operator)
            ->get('/helpdesk')
            ->assertOk()
            ->assertSee('Helpdesk Operator Panel');

        $this->actingAs($admin)
            ->get('/helpdesk')
            ->assertOk()
            ->assertSee('Helpdesk Operator Panel');
    }

    public function test_peserta_and_penyelenggara_are_bounced_from_helpdesk_queue_with_error_banner(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $penyelenggara = User::factory()->create(['role' => User::ROLE_PENYELENGGARA]);

        $responsePeserta = $this->actingAs($peserta)->get('/helpdesk');
        $responsePeserta->assertRedirect($peserta->home());
        $responsePeserta->assertSessionHas('error');

        $responsePenyelenggara = $this->actingAs($penyelenggara)->get('/helpdesk');
        $responsePenyelenggara->assertRedirect($penyelenggara->home());
        $responsePenyelenggara->assertSessionHas('error');
    }

    public function test_operator_hitting_admin_panel_is_bounced(): void
    {
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);

        // Accessing /admin directly when role is operator is forbidden by Filament auth/access rules (403 or redirect)
        $response = $this->actingAs($operator)->get('/admin');
        $this->assertTrue(in_array($response->status(), [403, 302], true));
    }

    public function test_migration_down_safely_migrates_operator_to_peserta(): void
    {
        $operator = User::factory()->create([
            'email' => 'operator_rollback_test@velosha.id',
            'role' => User::ROLE_OPERATOR,
        ]);

        $this->assertSame(User::ROLE_OPERATOR, $operator->fresh()->role);

        // Load the specific migration instance directly instead of migrate:rollback --step=1
        // to prevent rolling back unrelated newer migrations (like tickets table in Phase 2).
        $migrationFile = database_path('migrations/2026_10_24_000001_add_operator_to_users_role_enum.php');
        $migration = require $migrationFile;

        try {
            // Run down() directly on this specific migration
            $migration->down();

            $this->assertSame(User::ROLE_PESERTA, DB::table('users')->where('id', $operator->id)->value('role'));
        } finally {
            // Always run up() directly to restore schema for subsequent tests even if assertion fails
            $migration->up();
        }
    }

    /**
     * Pastikan akun operator demo dan tiket demo TIDAK PERNAH dibuat di environment production.
     */
    public function test_production_environment_never_creates_demo_operator_or_demo_tickets(): void
    {
        // Simulasikan environment production
        $originalEnv = app()->environment();
        app()->detectEnvironment(fn () => 'production');

        try {
            putenv('ADMIN_EMAIL=superadmin@velosha.id');
            putenv('ADMIN_PASSWORD=superSecretPassword123!');
            $this->artisan('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);

            // Di production: operator@velosha.id tidak boleh ada
            $this->assertDatabaseMissing('users', [
                'email' => 'operator@velosha.id',
            ]);

            // Di production: tiket demo tidak boleh ada
            $this->assertDatabaseMissing('helpdesk_tickets', [
                'subject' => 'Tidak bisa unduh e-ticket PDF',
            ]);
            $this->assertDatabaseMissing('helpdesk_tickets', [
                'subject' => 'Refund karena cedera sebelum race',
            ]);
        } finally {
            app()->detectEnvironment(fn () => $originalEnv);
        }
    }
}
