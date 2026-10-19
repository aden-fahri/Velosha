<?php

namespace Tests\Feature\Security;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_demo_accounts_in_production_seed(): void
    {
        putenv('ADMIN_EMAIL=admin@velosha.id');
        putenv('ADMIN_PASSWORD=SuperSecretPass123!');
        app()->detectEnvironment(fn () => 'production');
        config(['app.env' => 'production']);

        (new DatabaseSeeder())->run();

        $this->assertDatabaseMissing('users', ['email' => 'google.demo@velosha.id']);
        $this->assertDatabaseHas('users', ['email' => 'admin@velosha.id']);
        $this->assertDatabaseCount('registrations', 0);
    }
}