<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->columnSpan(1),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->columnSpan(1),
                TextInput::make('phone')
                    ->label('Nomor WhatsApp')
                    ->tel()
                    ->placeholder('081234567890')
                    ->default(null),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->helperText('Biarkan kosong saat edit jika tidak ingin mengubah sandi.'),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->default(null),
                Select::make('role')
                    ->label('Role (Peran)')
                    ->options([
                        User::ROLE_PESERTA => 'Peserta',
                        User::ROLE_PENYELENGGARA => 'Penyelenggara',
                        User::ROLE_ADMIN => 'Admin',
                    ])
                    ->default(User::ROLE_PESERTA)
                    ->required()
                    ->helperText('Peserta: rider. Penyelenggara: Race Director/Marshal. Admin: staf Velosha.'),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(['male' => 'Laki-laki', 'female' => 'Perempuan'])
                    ->default(null)
                    ->nullable(),
                Select::make('blood_type')
                    ->label('Golongan Darah')
                    ->options(['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O'])
                    ->default(null)
                    ->nullable(),
            ]);
    }
}
