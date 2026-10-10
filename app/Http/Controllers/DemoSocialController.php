<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DemoSocialController extends Controller
{
    /**
     * Provider social yang didukung (mode demo).
     *
     * CATATAN: ini simulasi OAuth. Untuk Socialite asli, ganti method ini
     * dengan Socialite::driver($provider)->redirect() + callback, lalu isi
     * GOOGLE_CLIENT_ID / FACEBOOK_CLIENT_ID / TIKTOK_CLIENT_ID di .env.
     */
    private const PROVIDERS = ['google', 'facebook', 'tiktok'];

    public function __invoke(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        $user = User::firstOrCreate(
            ['email' => "{$provider}.demo@velosha.id"],
            [
                'name' => 'Rider '.Str::ucfirst($provider),
                'provider' => $provider,
                'provider_id' => 'demo-'.$provider.'-001',
                'role' => User::ROLE_PESERTA,
            ],
        );

        Auth::login($user, remember: true);

        return redirect()->to($user->landingUrl());
    }
}
