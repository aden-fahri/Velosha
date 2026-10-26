<?php

namespace App\Livewire\Helpdesk;

use App\Services\Helpdesk\HelpdeskAnalytics;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @brief Komponen Livewire analitik dan metrik performa helpdesk untuk administrator.
 */
#[Layout('layouts.app', ['nav' => false])]
#[Title('Analitik Helpdesk — Administrator Velosha')]
class AdminAnalytics extends Component
{
    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman analitik khusus administrator.');
        }
    }

    public function render(HelpdeskAnalytics $analytics)
    {
        $kpi = $analytics->getKpiMetrics();
        $responseTimes = $analytics->getAverageResponseTimesPerOperator();
        $satisfaction = $analytics->getSatisfactionRatings();
        $topOperators = $analytics->getTopOperators();

        // Calculate max response time for CSS bar chart scaling
        $maxMinutes = $responseTimes->max('avg_response_minutes');
        if (! $maxMinutes || $maxMinutes <= 0) {
            $maxMinutes = 60.0;
        }

        return view('livewire.helpdesk.admin-analytics', [
            'kpi' => $kpi,
            'responseTimes' => $responseTimes,
            'satisfaction' => $satisfaction,
            'topOperators' => $topOperators,
            'maxMinutes' => $maxMinutes,
        ]);
    }
}
