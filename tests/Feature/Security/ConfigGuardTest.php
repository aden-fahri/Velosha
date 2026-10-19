<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class ConfigGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_flags_stay_false_in_production(): void
    {
        $process = new Process(
            ['php', '-d', 'error_reporting=0', '-r', 'require "vendor/autoload.php"; $app = require "bootstrap/app.php"; $app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap(); echo config("payments.simulation") ? "1" : "0"; echo config("app.demo_login") ? "1" : "0";'],
            base_path(),
            [
                'APP_ENV' => 'production',
                'PAYMENT_SIMULATION' => 'true',
                'APP_DEMO_LOGIN' => 'true',
                'DB_CONNECTION' => 'mysql',
                'DB_DATABASE' => 'velosha_test',
            ]
        );
        $process->run();
        $this->assertStringContainsString('00', $process->getOutput());
    }
}