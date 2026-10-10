<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Models\Registration;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = fn (): bool => (bool) Auth::user()?->isAdmin();

        return $table
            ->columns([
                TextColumn::make('bib_number')
                    ->label('BIB')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->placeholder('Belum terbit')
                    ->color('primary'),
                TextColumn::make('participant_name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('category.event.title')
                    ->label('Event')
                    ->searchable()
                    ->limit(25)
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Registration::STATUS_PAID => 'Lunas',
                        Registration::STATUS_CHECKED_IN => 'Check-in (RPC)',
                        Registration::STATUS_CANCELLED => 'Dibatalkan',
                        default => 'Menunggu Bayar',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Registration::STATUS_PAID => 'success',
                        Registration::STATUS_CHECKED_IN => 'info',
                        Registration::STATUS_CANCELLED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('identity_number')
                    ->label('NIK / Paspor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(fn (?string $state): string => $state === 'female' ? 'Perempuan' : 'Laki-laki')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('blood_type')
                    ->label('Gol. Darah')
                    ->badge()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('emergency_contact_phone')
                    ->label('Kontak Darurat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        Registration::STATUS_PAID => 'Lunas',
                        Registration::STATUS_CHECKED_IN => 'Check-in (RPC)',
                        Registration::STATUS_PENDING => 'Menunggu Bayar',
                        Registration::STATUS_CANCELLED => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                Action::make('checkIn')
                    ->label('Check-in RPC')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('info')
                    ->visible(fn (Registration $record): bool => $record->status === Registration::STATUS_PAID)
                    ->requiresConfirmation()
                    ->action(fn (Registration $record) => $record->update(['status' => Registration::STATUS_CHECKED_IN])),

                Action::make('markPaid')
                    ->label('Konfirmasi Lunas')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (Registration $record): bool => $record->status === Registration::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->action(function (Registration $record) {
                        try {
                            app(\App\Actions\CompleteRegistrationAction::class)->execute($record);
                            \Filament\Notifications\Notification::make()
                                ->title('Pendaftaran berhasil dikonfirmasi lunas.')
                                ->success()
                                ->send();
                        } catch (\RuntimeException $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal konfirmasi lunas')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('viewTicket')
                    ->label('E-Ticket')
                    ->icon(Heroicon::OutlinedTicket)
                    ->color('gray')
                    ->visible(fn (Registration $record): bool => $record->isPaid())
                    ->url(fn (Registration $record): string => route('ticket.show', $record))
                    ->openUrlInNewTab(),

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
