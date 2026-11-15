<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\HelpdeskKpiOverview;
use App\Services\Helpdesk\HelpdeskAnalytics as AnalyticsService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class HelpdeskAnalytics extends Page
{
    protected static ?string $navigationLabel = 'Analitik Helpdesk';

    protected static string | \UnitEnum | null $navigationGroup = 'Helpdesk';

    protected static ?string $title = 'Analitik & Performa Helpdesk';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.helpdesk-analytics';

    protected function getHeaderWidgets(): array
    {
        return [
            HelpdeskKpiOverview::class,
        ];
    }

    public static function canAccess(): bool
    {
        return (bool) Auth::user()?->isAdmin();
    }

    public function getViewData(): array
    {
        /** @var AnalyticsService $analytics */
        $analytics = app(AnalyticsService::class);

        $kpi = $analytics->getKpiMetrics();
        $responseTimes = $analytics->getAverageResponseTimesPerOperator();
        $satisfaction = $analytics->getSatisfactionRatings();
        $topOperators = $analytics->getTopOperators();

        $maxMinutes = $responseTimes->max('avg_response_minutes');
        if (! $maxMinutes || $maxMinutes <= 0) {
            $maxMinutes = 60.0;
        }

        return [
            'kpi' => $kpi,
            'responseTimes' => $responseTimes,
            'satisfaction' => $satisfaction,
            'topOperators' => $topOperators,
            'maxMinutes' => $maxMinutes,
        ];
    }
}
