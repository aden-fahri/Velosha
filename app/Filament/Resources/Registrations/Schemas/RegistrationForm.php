<?php

namespace App\Filament\Resources\Registrations\Schemas;

use App\Models\EventCategory;
use App\Models\Registration;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('status')
                    ->label('Status Pendaftaran')
                    ->options([
                        Registration::STATUS_PENDING => 'Menunggu Pembayaran',
                        Registration::STATUS_PAID => 'Lunas',
                        Registration::STATUS_CHECKED_IN => 'Checked-in (RPC Selesai)',
                        Registration::STATUS_CANCELLED => 'Dibatalkan',
                    ])
                    ->required()
                    ->columnSpan(1),

                TextInput::make('bib_number')
                    ->label('Nomor BIB')
                    ->placeholder('VLS-GF-0001')
                    ->maxLength(30)
                    ->columnSpan(1),

                Select::make('event_category_id')
                    ->label('Kategori Event')
                    ->options(fn () => EventCategory::with('event')->get()->mapWithKeys(
                        fn ($c) => [$c->id => ($c->event?->title ? $c->event->title.' — ' : '').$c->name]
                    ))
                    ->disabled()
                    ->columnSpan(2),

                TextInput::make('participant_name')
                    ->label('Nama Peserta')
                    ->required()
                    ->maxLength(150)
                    ->columnSpan(1),

                TextInput::make('identity_number')
                    ->label('NIK / Paspor')
                    ->required()
                    ->maxLength(50)
                    ->columnSpan(1),

                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->columnSpan(1),

                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ])
                    ->required()
                    ->columnSpan(1),

                Select::make('blood_type')
                    ->label('Golongan Darah')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'AB' => 'AB',
                        'O' => 'O',
                    ])
                    ->required()
                    ->columnSpan(1),

                TextInput::make('club')
                    ->label('Klub / Komunitas Sepeda')
                    ->maxLength(150)
                    ->columnSpan(1),

                TextInput::make('emergency_contact_name')
                    ->label('Nama Kontak Darurat')
                    ->required()
                    ->maxLength(150)
                    ->columnSpan(1),

                TextInput::make('emergency_contact_relation')
                    ->label('Hubungan Kontak Darurat')
                    ->required()
                    ->maxLength(50)
                    ->columnSpan(1),

                TextInput::make('emergency_contact_phone')
                    ->label('No. Telepon Darurat')
                    ->required()
                    ->tel()
                    ->maxLength(30)
                    ->columnSpan(2),

                Textarea::make('medical_notes')
                    ->label('Catatan Medis & Alergi')
                    ->rows(3)
                    ->columnSpan(2),
            ]);
    }
}
