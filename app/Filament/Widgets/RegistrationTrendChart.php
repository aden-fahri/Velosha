<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use App\Support\PanelScope;
use Filament\Widgets\ChartWidget;

class RegistrationTrendChart extends ChartWidget
{
    protected static ?int $sort = -8;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Tren Pendaftaran (6 Bulan Terakhir)';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $perBulan = PanelScope::registrations(Registration::query())
            ->pluck('created_at')
            ->filter()
            ->groupBy(fn ($tanggal) => $tanggal->format('Y-m'));

        $labels = [];
        $data = [];

        foreach (range(5, 0) as $mundur) {
            $bulan = now()->subMonths($mundur);
            $labels[] = $bulan->translatedFormat('M Y');
            $data[] = $perBulan->get($bulan->format('Y-m'))?->count() ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Pendaftaran masuk',
                'data' => $data,
                'fill' => false,
                'tension' => 0.35,
                'borderColor' => '#0f766e',
                'backgroundColor' => 'rgba(15, 118, 110, 0.12)',
                'pointBackgroundColor' => '#f59e0b',
                'pointBorderColor' => '#f59e0b',
            ]],
        ];
    }
}
