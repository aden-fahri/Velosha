<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Env;
use Tests\TestCase;

class ProductionConfigGuardTest extends TestCase
{
    /**
     * Prove that when config files are evaluated with APP_ENV=production,
     * app.demo_login and payments.simulation both evaluate to false,
     * even if APP_DEMO_LOGIN=true and PAYMENT_SIMULATION=true are set.
     */
    public function test_production_environment_forces_demo_login_and_payment_simulation_to_false(): void
    {
        $originalAppEnv = $_ENV['APP_ENV'] ?? null;
        $originalServerAppEnv = $_SERVER['APP_ENV'] ?? null;

        try {
            putenv('APP_ENV=production');
            $_ENV['APP_ENV'] = 'production';
            $_SERVER['APP_ENV'] = 'production';
            Env::getRepository()->set('APP_ENV', 'production');
            Env::getRepository()->set('APP_DEMO_LOGIN', 'true');
            Env::getRepository()->set('PAYMENT_SIMULATION', 'true');

            $appConfig = require base_path('config/app.php');
            $paymentsConfig = require base_path('config/payments.php');

            $this->assertFalse((bool) $appConfig['demo_login'], 'app.demo_login must evaluate to false when APP_ENV=production');
            $this->assertFalse((bool) $paymentsConfig['simulation'], 'payments.simulation must evaluate to false when APP_ENV=production');
        } finally {
            if ($originalAppEnv !== null) {
                putenv('APP_ENV=' . $originalAppEnv);
                $_ENV['APP_ENV'] = $originalAppEnv;
                $_SERVER['APP_ENV'] = $originalServerAppEnv;
                Env::getRepository()->set('APP_ENV', $originalAppEnv);
            }
        }
    }
}
