<?php

namespace App\Filament\Widgets;

use App\Services\Helpdesk\HelpdeskAnalytics;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class HelpdeskOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -5;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $analytics = app(HelpdeskAnalytics::class);

        $kpis = $analytics->getKpiMetrics();
        $satisfaction = $analytics->getSatisfactionRatings();
        $operatorTimes = $analytics->getAverageResponseTimesPerOperator();

        // Calculate overall average response time using existing HelpdeskAnalytics operator aggregates
        $totalResponses = $operatorTimes->sum('response_count');
        $totalMinutes = $operatorTimes->sum(function ($op) {
            return ($op->avg_response_minutes ?? 0) * ($op->response_count ?? 0);
        });

        $avgMinutes = $totalResponses > 0 ? ($totalMinutes / $totalResponses) : null;
        $formattedAvgResponse = $analytics->formatResponseTime($avgMinutes);

        $overallRating = $satisfaction['overall']['formatted_average'] ?? '-';

        return [
            Stat::make('Tiket Terbuka', (string) $kpis['open'])
                ->description('Menunggu penanganan')
                ->descriptionIcon('heroicon-m-inbox')
                ->color($kpis['open'] > 0 ? 'warning' : 'gray')
                ->url('/helpdesk/analitik'),

            Stat::make('Sedang Diproses', (string) $kpis['in_progress'])
                ->description('Dalam penanganan operator')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($kpis['in_progress'] > 0 ? 'info' : 'gray')
                ->url('/helpdesk/analitik'),

            Stat::make('Rata-rata Respons', $formattedAvgResponse)
                ->description('Waktu respons pertama')
                ->descriptionIcon('heroicon-m-clock')
                ->color($formattedAvgResponse !== '-' ? 'success' : 'gray')
                ->url('/helpdesk/analitik'),

            Stat::make('Rating Kepuasan', $overallRating)
                ->description('Buka analitik lengkap')
                ->descriptionIcon('heroicon-m-star')
                ->color($overallRating !== '-' ? 'success' : 'gray')
                ->url('/helpdesk/analitik'),
        ];
    }
}
