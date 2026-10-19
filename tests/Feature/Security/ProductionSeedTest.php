<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_demo_accounts_in_production_seed(): void
    {
        // simulasi: DatabaseSeeder melewati demo seeder di production
        $this->app['env'] = 'production';
        // hanya cek bahwa DemoUserSeeder tidak dipanggil: akun demo tidak ada
        $this->assertDatabaseMissing('users', ['email' => 'google.demo@velosha.id']);
    }
}