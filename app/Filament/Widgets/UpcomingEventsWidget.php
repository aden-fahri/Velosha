<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Support\PanelScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingEventsWidget extends TableWidget
{
    protected static ?int $sort = -4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return PanelScope::events(
            Event::query()
                ->published()
                ->upcoming()
                ->withCount('categories')
                ->withSum('categories as kuota_total', 'quota')
                ->withSum('categories as terisi_total', 'registered_count')
        )
            ->orderBy('event_date')
            ->limit(6);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Event Mendatang & Ketersediaan Kuota')
            ->columns([
                TextColumn::make('title')
                    ->label('Event')
                    ->limit(32)
                    ->weight('semibold'),
                TextColumn::make('event_date')
                    ->label('Tanggal')
                    ->date('d M Y'),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Event::TYPE_MEDIO_FONDO => 'Medio Fondo',
                        Event::TYPE_FUN_RIDE => 'Fun Ride',
                        Event::TYPE_CLIMB => 'KOM Climb',
                        default => 'Gran Fondo',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Event::TYPE_MEDIO_FONDO => 'info',
                        Event::TYPE_FUN_RIDE => 'success',
                        Event::TYPE_CLIMB => 'warning',
                        default => 'primary',
                    }),
                TextColumn::make('kuota_terisi')
                    ->label('Terisi')
                    ->state(fn (Event $record): string => number_format((int) $record->terisi_total)
                        .' / '
                        .number_format((int) $record->kuota_total)),
                TextColumn::make('categories_count')
                    ->label('Kategori')
                    ->state(fn (Event $record): int => (int) $record->categories_count),
            ])
            ->paginated(false);
    }
}
