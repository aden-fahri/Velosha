<?php

namespace Tests\Feature\Security;

use Database\Seeders\ProductionAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_empty_password(): void
    {
        putenv('ADMIN_EMAIL=admin@velosha.id');
        putenv('ADMIN_PASSWORD=');
        $this->expectException(\RuntimeException::class);
        (new ProductionAdminSeeder())->run();
    }

    public function test_rejects_weak_password(): void
    {
        putenv('ADMIN_EMAIL=admin@velosha.id');
        putenv('ADMIN_PASSWORD=password');
        $this->expectException(\RuntimeException::class);
        (new ProductionAdminSeeder())->run();
    }
}