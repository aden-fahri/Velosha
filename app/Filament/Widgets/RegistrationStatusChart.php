<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use App\Support\PanelScope;
use Filament\Widgets\ChartWidget;

class RegistrationStatusChart extends ChartWidget
{
    protected static ?int $sort = -6;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Komposisi Status Pendaftaran';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $hitung = PanelScope::registrations(Registration::query())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'labels' => ['Lunas', 'Check-in RPC', 'Menunggu Bayar', 'Dibatalkan'],
            'datasets' => [[
                'label' => 'Pendaftaran',
                'data' => [
                    (int) $hitung->get(Registration::STATUS_PAID, 0),
                    (int) $hitung->get(Registration::STATUS_CHECKED_IN, 0),
                    (int) $hitung->get(Registration::STATUS_PENDING, 0),
                    (int) $hitung->get(Registration::STATUS_CANCELLED, 0),
                ],
                'backgroundColor' => ['#0f766e', '#0284c7', '#f59e0b', '#dc2626'],
                'borderWidth' => 0,
            ]],
        ];
    }
}
