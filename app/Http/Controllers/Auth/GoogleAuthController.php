<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman consent screen Google OAuth.
     */
    public function redirect(): RedirectResponse
    {
        // Jika kredensial belum diisi dan mode demo aktif, fallback ke demo login agar tidak error fatal saat testing
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            if (config('app.demo_login')) {
                return redirect()->route('auth.demo', ['provider' => 'google']);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'Google Client ID dan Secret belum dikonfigurasi di file .env.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google setelah otorisasi pengguna.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal masuk dengan Google. Silakan coba kembali atau gunakan email dan kata sandi.',
            ]);
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google Anda tidak menyediakan alamat email publik.',
            ]);
        }

        $rawUser = $googleUser->user ?? [];
        $emailVerified = $rawUser['email_verified'] ?? false;
        $isVerified = ($emailVerified === true || $emailVerified === 'true');

        if (! $isVerified) {
            return redirect()->route('login')->withErrors([
                'email' => 'Alamat email Google Anda belum diverifikasi. Harap verifikasi akun Google Anda terlebih dahulu.',
            ]);
        }

        // Cari berdasarkan google_id atau email yang terdaftar
        $user = User::where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                // Tautkan akun yang sudah ada
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                ]);
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Peserta Velosha',
                    'email' => $email,
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => User::ROLE_PESERTA,
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            // Perbarui avatar jika ada
            if ($googleUser->getAvatar() && !$user->avatar) {
                $user->update(['avatar' => $googleUser->getAvatar()]);
            }
        }

        Auth::login($user, remember: true);

        return redirect()->intended($user->landingUrl());
    }
}

