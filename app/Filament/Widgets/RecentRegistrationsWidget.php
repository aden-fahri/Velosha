<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RecentRegistrationsWidget extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.recent-registrations';
}
