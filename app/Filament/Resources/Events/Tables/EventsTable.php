<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                TextColumn::make('organization.name')
                    ->label('Komunitas')
                    ->limit(20)
                    ->placeholder('Independen')
                    ->sortable(),
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
                        Event::STATUS_SUBMITTED => 'Menunggu Moderasi',
                        Event::STATUS_REJECTED => 'Ditolak',
                        Event::STATUS_COMPLETED => 'Selesai',
                        Event::STATUS_CANCELLED => 'Dibatalkan',
                        default => 'Draft',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Event::STATUS_PUBLISHED => 'success',
                        Event::STATUS_SUBMITTED => 'warning',
                        Event::STATUS_REJECTED => 'danger',
                        Event::STATUS_COMPLETED => 'info',
                        Event::STATUS_CANCELLED => 'danger',
                        default => 'gray',
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
                SelectFilter::make('status')
                    ->label('Status Event')
                    ->options(\App\Enums\EventStatus::options()),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Event $record): bool => Auth::user()?->isAdmin() && $record->status === Event::STATUS_SUBMITTED)
                    ->requiresConfirmation()
                    ->modalHeading('Setujui & Publikasikan Race')
                    ->modalDescription('Event ini akan resmi dipublikasikan ke peserta dan 1 kuota organisasi akan terpotong secara otomatis.')
                    ->action(function (Event $record) {
                        try {
                            $record->approve(Auth::user());
                            Notification::make()
                                ->title('Event disetujui & dipublikasikan!')
                                ->success()
                                ->send();
                        } catch (\DomainException $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Event $record): bool => Auth::user()?->isAdmin() && $record->status === Event::STATUS_SUBMITTED)
                    ->form([
                        Textarea::make('rejection_note')
                            ->label('Alasan Penolakan')
                            ->placeholder('Tuliskan poin-poin yang perlu diperbaiki...')
                            ->required(),
                    ])
                    ->action(function (Event $record, array $data) {
                        $record->reject(Auth::user(), $data['rejection_note']);
                        Notification::make()
                            ->title('Event ditolak.')
                            ->warning()
                            ->send();
                    }),
                Action::make('submitReview')
                    ->label('Ajukan Moderasi')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (Event $record): bool => in_array($record->status, [Event::STATUS_DRAFT, Event::STATUS_REJECTED], true))
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Event untuk Ditinjau')
                    ->modalDescription('Event akan dikirim ke tim admin Velosha untuk diverifikasi sebelum dipublikasikan.')
                    ->action(function (Event $record) {
                        try {
                            $record->submitForReview();
                            Notification::make()
                                ->title('Event berhasil diajukan untuk moderasi.')
                                ->info()
                                ->send();
                        } catch (\DomainException $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
