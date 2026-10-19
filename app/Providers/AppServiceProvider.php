<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as FilamentLogoutResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            FilamentLogoutResponse::class,
            LogoutResponse::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Semua halaman (termasuk tanpa komponen Livewire) mendapat aset Livewire
        // agar wire:navigate berfungsi di seluruh situs.
        Livewire::forceAssetInjection();

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('mizu', function (Request $request) {
            return [
                Limit::perMinute(10)->by('mizu-min:'.($request->user()?->id ?: $request->ip())),
                Limit::perDay(50)->by('mizu-day:'.($request->user()?->id ?: $request->ip())),
            ];
        });
    }
}
