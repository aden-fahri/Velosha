<?php

namespace App\Filament\Widgets;

use App\Services\Helpdesk\HelpdeskAnalytics;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HelpdeskKpiOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    /**
     * Sembunyikan dari Dashboard utama admin karena widget ini didedikasikan
     * untuk halaman Analitik & Performa Helpdesk.
     */
    public static function canView(): bool
    {
        return false;
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        /** @var HelpdeskAnalytics $analytics */
        $analytics = app(HelpdeskAnalytics::class);
        $kpi = $analytics->getKpiMetrics();

        return [
            Stat::make('Total Tiket Masuk', number_format($kpi['total'] ?? 0))
                ->description('seluruh riwayat tiket')
                ->icon(Heroicon::InboxStack),

            Stat::make('Tiket Baru (Open)', number_format($kpi['open'] ?? 0))
                ->description('menunggu respons awal')
                ->color($kpi['open'] > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::Envelope),

            Stat::make('Sedang Diproses', number_format($kpi['in_progress'] ?? 0))
                ->description('ditangani oleh operator')
                ->color($kpi['in_progress'] > 0 ? 'info' : 'gray')
                ->icon(Heroicon::ArrowPath),

            Stat::make('Selesai (Closed)', number_format($kpi['closed'] ?? 0))
                ->description('telah terselesaikan')
                ->color('success')
                ->icon(Heroicon::CheckBadge),
        ];
    }
}
