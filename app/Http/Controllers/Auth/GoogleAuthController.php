<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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

        $providerId = $googleUser->getId();
        $email = $googleUser->getEmail();

        // 1. Validasi keberadaan dan format payload Google User
        $validator = Validator::make([
            'provider_id' => $providerId,
            'email' => $email,
        ], [
            'provider_id' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255'],
        ], [
            'provider_id.required' => 'ID akun Google tidak valid.',
            'email.required' => 'Akun Google Anda tidak menyediakan alamat email publik.',
            'email.email' => 'Format email dari akun Google tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')->withErrors([
                'email' => $validator->errors()->first('email') ?: 'Data akun Google tidak valid.',
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

        $normalizedEmail = Str::lower(trim($email));

        // 2. Cari berdasarkan google_id atau email yang terdaftar
        $user = User::where('provider', 'google')
            ->where('provider_id', $providerId)
            ->first();

        if ($user) {
            // Validasi keamanan: Blokir jika role bukan peserta atau akun sistem
            if ($user->isSystem() || in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PENYELENGGARA, User::ROLE_OPERATOR], true) || $user->role !== User::ROLE_PESERTA) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun ini dikelola dengan kata sandi. Masuk dengan email dan kata sandi.',
                ]);
            }

            // Perbarui avatar jika ada dan user belum punya avatar
            $avatar = $this->sanitizeAvatarUrl($googleUser->getAvatar());
            if ($avatar && ! $user->avatar) {
                $user->update(['avatar' => $avatar]);
            }
        } else {
            // Jika belum terhubung via provider_id, cari via email lokal
            $user = User::where('email', $normalizedEmail)->first();

            if ($user) {
                // Jangan tautkan akun istimewa (admin, penyelenggara, operator) atau akun sistem
                if ($user->isSystem() || in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PENYELENGGARA, User::ROLE_OPERATOR], true) || $user->role !== User::ROLE_PESERTA) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Akun ini dikelola dengan kata sandi. Masuk dengan email dan kata sandi.',
                    ]);
                }

                // Jangan tautkan jika akun lokal belum terverifikasi
                if (is_null($user->email_verified_at)) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Alamat email akun Anda belum diverifikasi. Silakan verifikasi email Anda atau masuk dengan kata sandi.',
                    ]);
                }

                // Tautkan akun peserta yang sudah terverifikasi
                $avatar = $this->sanitizeAvatarUrl($googleUser->getAvatar());
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $providerId,
                    'avatar' => $user->avatar ?: $avatar,
                ]);
            } else {
                // Buat user baru (role peserta, email_verified_at sekarang, password NULL)
                $rawName = trim($googleUser->getName() ?? $googleUser->getNickname() ?? 'Peserta Velosha');
                $name = mb_substr($rawName !== '' ? $rawName : 'Peserta Velosha', 0, 255);
                $avatar = $this->sanitizeAvatarUrl($googleUser->getAvatar());

                $user = User::create([
                    'name' => $name,
                    'email' => $normalizedEmail,
                    'provider' => 'google',
                    'provider_id' => $providerId,
                    'avatar' => $avatar,
                    'role' => User::ROLE_PESERTA,
                ]);
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended($user->landingUrl());
    }

    /**
     * Sanitasi dan validasi format URL avatar agar aman.
     */
    protected function sanitizeAvatarUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (! in_array(strtolower($scheme ?: ''), ['http', 'https'], true)) {
            return null;
        }

        return mb_substr($url, 0, 2048);
    }
}

