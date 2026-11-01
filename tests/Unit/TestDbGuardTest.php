<?php

namespace Tests\Unit;

use RuntimeException;
use Tests\TestCase;

class TestDbGuardTest extends TestCase
{
    public function test_guard_allows_dedicated_test_database(): void
    {
        $originalDefault = config('database.default');
        $originalDb = config("database.connections.{$originalDefault}.database");

        try {
            config(["database.connections.{$originalDefault}.database" => 'velosha_test']);
            $this->assertDatabaseIsTestSafe();
            $this->assertTrue(true);
        } finally {
            config(["database.connections.{$originalDefault}.database" => $originalDb]);
        }
    }

    public function test_guard_allows_in_memory_sqlite_for_ci(): void
    {
        $originalDefault = config('database.default');
        $originalSqliteDb = config('database.connections.sqlite.database');

        try {
            config(['database.default' => 'sqlite']);
            config(['database.connections.sqlite.database' => ':memory:']);

            $this->assertDatabaseIsTestSafe();
            $this->assertTrue(true);
        } finally {
            config(['database.default' => $originalDefault]);
            config(['database.connections.sqlite.database' => $originalSqliteDb]);
        }
    }

    public function test_guard_blocks_development_or_production_database(): void
    {
        $originalDefault = config('database.default');
        $originalDb = config("database.connections.{$originalDefault}.database");

        try {
            config(['database.default' => 'mysql']);
            config(['database.connections.mysql.database' => 'velosha']); // Dev DB without _test

            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Refusing to run tests on non-test database');

            $this->assertDatabaseIsTestSafe();
        } finally {
            config(['database.default' => $originalDefault]);
            config(["database.connections.{$originalDefault}.database" => $originalDb]);
        }
    }
}
