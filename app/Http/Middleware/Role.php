<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Batasi akses route berdasarkan role pengguna.
     *
     * Contoh penggunaan: ->middleware('role:peserta')
     * atau kombinasi:      ->middleware(['auth', 'role:admin,penyelenggara'])
     *
     * Saat role tidak cocok, pengguna DIPANTULKAN ke halaman asalnya
     * (bila aman) dengan banner peringatan, bukan dibawa senyap ke panel.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        $allowed = implode(' / ', $roles);
        $message = "Halaman ini hanya untuk peran {$allowed}. Anda login sebagai {$user->role} — "
            .'silakan keluar dan masuk dengan akun yang sesuai.';

        return redirect()
            ->to($this->fallbackUrl($request))
            ->with('error', $message);
    }

    /**
     * URL tujuan saat role ditolak: halaman asal bila aman,
     * selain itu kembali ke home() role tersebut.
     */
    protected function fallbackUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');

        if (! $this->isSafeReferer($referer, $request)) {
            return $request->user()->home();
        }

        return $referer;
    }

    /**
     * Referer aman = dari host yang sama, bukan path yang sama
     * (anti-loop), dan bukan halaman panel/auth (anti-bounce-berputar).
     */
    protected function isSafeReferer(?string $referer, Request $request): bool
    {
        if ($referer === null || $referer === '') {
            return false;
        }

        $host = parse_url($referer, PHP_URL_HOST);
        $path = parse_url($referer, PHP_URL_PATH) ?: '/';

        if ($host !== null && $host !== $request->getHost()) {
            return false;
        }

        if ($path === $request->getPathInfo()) {
            return false;
        }

        if (str_starts_with($path, '/admin') || str_starts_with($path, '/auth/')) {
            return false;
        }

        $authPaths = ['/login', '/register', '/forgot-password', '/reset-password', '/verify-email', '/confirm-password'];

        foreach ($authPaths as $authPath) {
            if ($path === $authPath || str_starts_with($path, $authPath.'/')) {
                return false;
            }
        }

        return true;
    }
}
