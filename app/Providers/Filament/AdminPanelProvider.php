<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\RecentRegistrationsWidget;
use App\Filament\Widgets\RegistrationStatusChart;
use App\Filament\Widgets\RegistrationTrendChart;
use App\Filament\Widgets\UpcomingEventsWidget;
use App\Filament\Widgets\VeloshaAccountWidget;
use App\Filament\Widgets\VeloshaStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Velosha')
            ->brandLogo('/images/logo-velosha.png')
            ->favicon('/images/logo-velosha.png')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->spa()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                VeloshaStatsOverview::class,
                RegistrationTrendChart::class,
                RegistrationStatusChart::class,
                UpcomingEventsWidget::class,
                RecentRegistrationsWidget::class,
                VeloshaAccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => self::gridAddCardStyle(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => self::panelFlashBanner(),
            );
    }

    /**
     * Banner flash session('error') untuk panel: layout Filament tidak
     * punya slot banner seperti layouts/app, padahal Role middleware sering
     * memantulkan pengguna ke /admin dengan pesan peringatan.
     */
    protected static function panelFlashBanner(): string
    {
        $error = session('error');

        if (blank($error)) {
            return '';
        }

        $error = e($error);

        return <<<HTML
            <div class="velo-panel-flash" style="position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 8px 16px; background: #fef2f2; border-bottom: 1px solid #fecaca; color: #b91c1c; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 12px; font-weight: 700; text-align: center; box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);">
                <span style="display: inline-flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 12px;">
                    <span>{$error}</span>
                    <button type="button" onclick="this.closest('.velo-panel-flash').remove()" style="background: #ffffff; border: 1px solid #fecaca; border-radius: 3px; color: #b91c1c; font-size: 11px; font-weight: 700; padding: 3px 9px; cursor: pointer;">Tutup</button>
                </span>
            </div>
            HTML;
    }

    /**
     * Kartu "+ Tambah" untuk repeater grid 2 kolom: mengisi slot kosong
     * di baris terakhir (grid ganjil) alih-alih tombol di bawah daftar.
     */
    protected static function gridAddCardStyle(): string
    {
        return <<<'HTML'
            <style>
                .repeater-add-grid {
                    grid-template-columns: minmax(0, 1fr);
                    column-gap: calc(var(--spacing) * 4);
                    align-items: start;
                }

                @media (min-width: 64rem) {
                    .repeater-add-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                    }
                }

                .repeater-add-grid > .fi-fo-repeater-actions {
                    grid-column: 1 / -1;
                }

                .repeater-add-grid > ul.fi-fo-repeater-items {
                    display: contents;
                }

                .repeater-add-grid > ul.fi-fo-repeater-items > li:nth-child(odd):last-child {
                    grid-column: 1 / -1;
                }

                .repeater-add-grid > .fi-fo-repeater-add {
                    grid-column: 1 / -1;
                }

                .repeater-add-grid > .fi-fo-repeater-add > .fi-btn.fi-size-sm {
                    width: 100%;
                    min-height: 4.5rem;
                    margin-block: 0;
                    padding: 1rem;
                    background-color: transparent;
                    background-image: none;
                    box-shadow: none;
                    border: 2px dashed #d1d5db;
                    border-radius: var(--radius-xl);
                    color: #556066;
                }

                .repeater-add-grid > .fi-fo-repeater-add > .fi-btn.fi-size-sm:hover {
                    background-color: #f9fafb;
                    border-color: #9ca3af;
                    color: #111827;
                }

                .repeater-add-grid > .fi-fo-repeater-add > .fi-btn.fi-size-sm:active {
                    background-color: #f3f4f6;
                    border-color: #6b7280;
                }

                .dark .repeater-add-grid > .fi-fo-repeater-add > .fi-btn.fi-size-sm {
                    border-color: var(--gray-700);
                    color: var(--gray-400);
                }

                .dark .repeater-add-grid > .fi-fo-repeater-add > .fi-btn.fi-size-sm:hover {
                    background-color: color-mix(in oklab, var(--gray-800) 50%, transparent);
                    border-color: var(--gray-600);
                    color: var(--gray-200);
                }
            </style>
            HTML;
    }
}
