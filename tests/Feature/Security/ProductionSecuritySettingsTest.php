<?php

namespace Tests\Feature\Security;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProductionSecuritySettingsTest extends TestCase
{
    public function test_env_example_has_secure_cookie_configured(): void
    {
        $envExample = file_get_contents(base_path('.env.example'));

        $this->assertMatchesRegularExpression('/SESSION_SECURE_COOKIE\s*=\s*true/', $envExample);
        $this->assertStringContainsString('# set to true in production over HTTPS', $envExample);
    }

    public function test_production_environment_forces_https_scheme(): void
    {
        $originalEnv = app()->environment();

        try {
            $this->app['env'] = 'production';

            $provider = new AppServiceProvider($this->app);
            $provider->boot();

            $generatedUrl = URL::to('/test-secure-url');
            $this->assertStringStartsWith('https://', $generatedUrl);
        } finally {
            $this->app['env'] = $originalEnv;
            URL::forceScheme(null);
        }
    }
}
