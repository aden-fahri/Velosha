<?php

namespace App\Filament\Resources\PayoutRequests\Tables;

use App\Models\PayoutRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PayoutRequestsTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = fn (): bool => (bool) Auth::user()?->isAdmin();

        return $table
            ->columns([
                TextColumn::make('organization.name')
                    ->label('Komunitas / EO')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Nominal Penarikan')
                    ->formatStateUsing(fn (int $state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('account_info')
                    ->label('Rekening Tujuan')
                    ->state(fn (PayoutRequest $record): string => $record->account
                        ? $record->account->bank_name . ' (' . $record->account->masked_account_number . ' a.n. ' . $record->account->account_name . ')'
                        : '—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PayoutRequest::STATUS_APPROVED => 'Ditransfer',
                        PayoutRequest::STATUS_PENDING => 'Menunggu Transfer',
                        PayoutRequest::STATUS_REJECTED => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        PayoutRequest::STATUS_APPROVED => 'success',
                        PayoutRequest::STATUS_PENDING => 'warning',
                        PayoutRequest::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('requester.name')
                    ->label('Diajukan Oleh')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label('No. Ref Bank')
                    ->placeholder('—')
                    ->fontFamily('mono'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pencairan')
                    ->options([
                        PayoutRequest::STATUS_PENDING => 'Menunggu Transfer',
                        PayoutRequest::STATUS_APPROVED => 'Ditransfer (Disetujui)',
                        PayoutRequest::STATUS_REJECTED => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui & Transfer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PayoutRequest $record): bool => $isAdmin() && $record->isPending())
                    ->form([
                        TextInput::make('reference_number')
                            ->label('Nomor Referensi Transfer Bank')
                            ->placeholder('Contoh: TRF-BCA-20261109-082')
                            ->required(),
                    ])
                    ->action(function (PayoutRequest $record, array $data) {
                        try {
                            $record->approve(Auth::user(), $data['reference_number']);
                            Notification::make()
                                ->title('Pencairan dana berhasil disetujui & didebit dari buku kas.')
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
                    ->visible(fn (PayoutRequest $record): bool => $isAdmin() && $record->isPending())
                    ->form([
                        Textarea::make('rejection_note')
                            ->label('Alasan Penolakan')
                            ->placeholder('Tuliskan alasan penolakan transfer dana...')
                            ->required(),
                    ])
                    ->action(function (PayoutRequest $record, array $data) {
                        $record->reject(Auth::user(), $data['rejection_note']);
                        Notification::make()
                            ->title('Pengajuan pencairan dana ditolak.')
                            ->warning()
                            ->send();
                    }),
            ]);
    }
}
