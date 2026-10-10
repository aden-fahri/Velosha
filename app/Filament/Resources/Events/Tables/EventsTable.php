<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = fn (): bool => (bool) Auth::user()?->isAdmin();

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Event::STATUS_PUBLISHED => 'Published',
                        Event::STATUS_COMPLETED => 'Selesai',
                        Event::STATUS_CANCELLED => 'Dibatalkan',
                        default => 'Draft',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Event::STATUS_PUBLISHED => 'success',
                        Event::STATUS_COMPLETED => 'info',
                        Event::STATUS_CANCELLED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('event_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('location_name')
                    ->label('Lokasi')
                    ->limit(25),
                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->state(fn (Event $record): int => $record->categories()->count()),
                TextColumn::make('lowestPrice')
                    ->label('Harga Min.')
                    ->state(fn (Event $record): string => $record->lowestPrice === null
                        ? '—'
                        : 'Rp '.number_format($record->lowestPrice, 0, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible($isAdmin),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible($isAdmin),
                ]),
            ]);
    }
}
