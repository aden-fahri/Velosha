<?php

namespace Tests\Feature\Security;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSeedTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
        parent::tearDown();
    }

    public function test_no_demo_accounts_in_production_seed(): void
    {
        putenv('ADMIN_EMAIL=owner@contoh.test');
        putenv('ADMIN_PASSWORD=SuperSecretPass123!');
        app()->detectEnvironment(fn () => 'production');
        config(['app.env' => 'production']);

        (new DatabaseSeeder())->run();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => 'owner@contoh.test']);
        $this->assertDatabaseMissing('users', ['email' => 'admin@velosha.id']);
        $this->assertDatabaseMissing('users', ['email' => 'penyelenggara@velosha.id']);
        $this->assertDatabaseMissing('users', ['email' => 'peserta@velosha.id']);
        $this->assertDatabaseMissing('users', ['email' => 'system-bot@velosha.id']);
        $this->assertDatabaseMissing('users', ['is_system' => true]);
        $this->assertDatabaseCount('registrations', 0);
    }
}