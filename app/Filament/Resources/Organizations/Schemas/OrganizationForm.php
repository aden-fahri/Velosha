<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Models\Organization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdmin = fn (): bool => (bool) Auth::user()?->isAdmin();

        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nama Komunitas / EO')
                    ->required()
                    ->maxLength(150),
                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled(),
                Textarea::make('description')
                    ->label('Deskripsi Komunitas')
                    ->columnSpan(2),
                Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options([
                        Organization::VERIFICATION_UNVERIFIED => 'Belum Mengajukan',
                        Organization::VERIFICATION_PENDING => 'Menunggu Peninjauan Admin (Pending)',
                        Organization::VERIFICATION_VERIFIED => 'Terverifikasi (Verified)',
                        Organization::VERIFICATION_REJECTED => 'Ditolak (Rejected)',
                    ])
                    ->disabled(fn (): bool => ! $isAdmin())
                    ->required(),
                Textarea::make('rejection_note')
                    ->label('Catatan Penolakan Verifikasi')
                    ->columnSpan(2)
                    ->disabled(fn (): bool => ! $isAdmin()),
            ]);
    }
}

