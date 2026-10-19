<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\ProductionAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
        parent::tearDown();
    }

    public function test_rejects_empty_password(): void
    {
        putenv('ADMIN_EMAIL=admin@velosha.id');
        putenv('ADMIN_PASSWORD=');

        try {
            (new ProductionAdminSeeder())->run();
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('wajib', $e->getMessage());
        }
    }

    public function test_rejects_weak_password(): void
    {
        putenv('ADMIN_EMAIL=admin@velosha.id');
        putenv('ADMIN_PASSWORD=password');

        try {
            (new ProductionAdminSeeder())->run();
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('lemah', $e->getMessage());
        }
    }

    public function test_creates_admin_with_strong_password(): void
    {
        putenv('ADMIN_EMAIL=admin@velosha.id');
        putenv('ADMIN_PASSWORD=SuperSecretPass123!');

        (new ProductionAdminSeeder())->run();

        $this->assertDatabaseHas('users', ['email' => 'admin@velosha.id', 'role' => 'admin']);
    }
}