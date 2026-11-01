<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_endpoint_is_rate_limited_to_5_per_minute(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->post('/register', [
                'email' => 'invalid-email',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode(), "Request {$i} should not be throttled");
        }

        // 6th attempt must be throttled with HTTP 429 Too Many Requests
        $response = $this->post('/register', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(429);
    }

    public function test_forgot_password_endpoint_is_rate_limited_to_5_per_minute(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->post('/forgot-password', [
                'email' => "someone{$i}@velosha.test",
            ]);
            $this->assertNotEquals(429, $response->getStatusCode(), "Request {$i} should not be throttled");
        }

        // 6th attempt must be throttled with HTTP 429 Too Many Requests
        $response = $this->post('/forgot-password', [
            'email' => 'someone6@velosha.test',
        ]);

        $response->assertStatus(429);
    }

    public function test_login_endpoint_is_rate_limited_after_5_failed_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'rider@velosha.test',
            'password' => bcrypt('correct-password'),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'rider@velosha.test',
                'password' => 'wrong-password',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th failed login attempt triggers throttle validation error
        $response = $this->post('/login', [
            'email' => 'rider@velosha.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login', $errorMessage);
    }
}
