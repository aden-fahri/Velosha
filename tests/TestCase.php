<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertDatabaseIsTestSafe();
        Http::preventStrayRequests();
    }

    public function assertDatabaseIsTestSafe(): void
    {
        $defaultConnection = config('database.default');
        $database = config("database.connections.{$defaultConnection}.database");

        $isInMemorySqlite = ($defaultConnection === 'sqlite' && ($database === ':memory:' || empty($database)));
        $isDedicatedTestDb = is_string($database) && str_ends_with($database, '_test');

        if (! $isInMemorySqlite && ! $isDedicatedTestDb) {
            throw new \RuntimeException(sprintf(
                'Refusing to run tests on non-test database! Connection: [%s], Database: [%s]. Database must be sqlite ":memory:" or its name must end with "_test".',
                $defaultConnection,
                $database ?? 'null'
            ));
        }
    }
}
