<?php

namespace App\Filament\Resources\Organizations\Tables;

use App\Models\Organization;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = fn (): bool => (bool) Auth::user()?->isAdmin();

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Komunitas / EO')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('verification_status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Organization::VERIFICATION_VERIFIED => 'Terverifikasi',
                        Organization::VERIFICATION_PENDING => 'Menunggu Review',
                        Organization::VERIFICATION_REJECTED => 'Ditolak',
                        default => 'Belum Mengajukan',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Organization::VERIFICATION_VERIFIED => 'success',
                        Organization::VERIFICATION_PENDING => 'warning',
                        Organization::VERIFICATION_REJECTED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('quotaBalance')
                    ->label('Saldo Kuota')
                    ->state(fn (Organization $record): int => $record->quotaBalance())
                    ->suffix(' Event')
                    ->sortable(),
                TextColumn::make('events_count')
                    ->label('Total Event')
                    ->state(fn (Organization $record): int => $record->events()->count()),
                TextColumn::make('verified_at')
                    ->label('Tgl Diverifikasi')
                    ->dateTime('d M Y')
                    ->placeholder('—'),
                TextColumn::make('verifier.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('verification_status')
                    ->label('Filter Status')
                    ->options([
                        Organization::VERIFICATION_UNVERIFIED => 'Belum Mengajukan',
                        Organization::VERIFICATION_PENDING => 'Menunggu Review',
                        Organization::VERIFICATION_VERIFIED => 'Terverifikasi',
                        Organization::VERIFICATION_REJECTED => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('downloadDocument')
                    ->label('Dokumen')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->visible(fn (Organization $record): bool => ! empty($record->verification_document_path))
                    ->url(fn (Organization $record): string => route('organizations.document.download', ['organization' => $record->slug]))
                    ->openUrlInNewTab(),
                Action::make('approveVerification')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Organization $record): bool => $isAdmin() && $record->verification_status !== Organization::VERIFICATION_VERIFIED)
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Verifikasi Penyelenggara')
                    ->modalDescription('Komunitas ini akan resmi berstatus TERVERIFIKASI dan diizinkan mengajukan race ke publik.')
                    ->action(function (Organization $record) {
                        $record->approveVerification(Auth::user());
                        Notification::make()
                            ->title("Komunitas '{$record->name}' berhasil diverifikasi!")
                            ->success()
                            ->send();
                    }),
                Action::make('rejectVerification')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Organization $record): bool => $isAdmin() && $record->verification_status !== Organization::VERIFICATION_REJECTED)
                    ->form([
                        Textarea::make('rejection_note')
                            ->label('Alasan Penolakan')
                            ->placeholder('Tuliskan alasan penolakan dan dokumen yang perlu dilengkapi...')
                            ->required(),
                    ])
                    ->action(function (Organization $record, array $data) {
                        $record->rejectVerification(Auth::user(), $data['rejection_note']);
                        Notification::make()
                            ->title("Verifikasi komunitas '{$record->name}' ditolak.")
                            ->warning()
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }
}

