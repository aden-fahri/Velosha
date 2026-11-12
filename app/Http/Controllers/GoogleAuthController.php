<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman consent Google OAuth resmi.
     */
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        if (! $clientId || ! config('services.google.client_secret')) {
            return redirect()->route('auth.demo', ['provider' => 'google']);
        }

        $state = Str::random(40);
        session()->put('google_oauth_state', $state);

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'access_type' => 'online',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    /**
     * Callback dari Google OAuth setelah otorisasi pengguna.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            Log::warning('Google OAuth callback error', ['error' => $request->query('error')]);
            return redirect()->route('login')->with('error', 'Otorisasi Google dibatalkan atau tidak disetujui.');
        }

        $code = $request->query('code');
        if (! $code) {
            return redirect()->route('login')->with('error', 'Kode otorisasi dari Google tidak ditemukan.');
        }

        try {
            // 1. Tukar authorization code dengan access token
            $tokenRes = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.google.redirect'),
            ]);

            if (! $tokenRes->successful()) {
                Log::error('Google OAuth token exchange failed', ['status' => $tokenRes->status(), 'body' => $tokenRes->body()]);
                return redirect()->route('login')->with('error', 'Gagal memverifikasi token dari Google. Silakan coba kembali.');
            }

            $tokenData = $tokenRes->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (! $accessToken) {
                return redirect()->route('login')->with('error', 'Access token Google tidak valid.');
            }

            // 2. Ambil profil pengguna resmi dari Google API
            $userRes = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
            if (! $userRes->successful()) {
                Log::error('Google OAuth userinfo failed', ['status' => $userRes->status(), 'body' => $userRes->body()]);
                return redirect()->route('login')->with('error', 'Gagal mengambil data profil dari Google.');
            }

            $googleData = $userRes->json();
            $email = $googleData['email'] ?? null;
            $name = $googleData['name'] ?? 'Rider Velosha';
            $googleId = $googleData['sub'] ?? null;
            $avatar = $googleData['picture'] ?? null;

            if (! $email) {
                return redirect()->route('login')->with('error', 'Email tidak ditemukan dari akun Google.');
            }

            // 3. Cari pengguna yang sudah ada atau daftarkan baru sebagai Peserta
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $googleId,
                    'avatar' => $user->avatar ?: $avatar,
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)),
                    'role' => User::ROLE_PESERTA,
                    'provider' => 'google',
                    'provider_id' => $googleId,
                    'avatar' => $avatar,
                ]);
            }

            // Pastikan user adalah peserta jika login publik via Google
            if (! $user->isAdmin() && ! $user->isOperator() && ! $user->isPenyelenggara()) {
                $user->update(['role' => User::ROLE_PESERTA]);
            }

            Auth::login($user, remember: true);

            return redirect()->intended($user->landingUrl());
        } catch (\Throwable $e) {
            Log::error('Google OAuth exception: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kendala saat login dengan Google: ' . $e->getMessage());
        }
    }
}
