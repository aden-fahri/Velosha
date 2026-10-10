<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Event;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('Judul Event')
                    ->maxLength(200)
                    ->required()
                    ->columnSpan(2),
                TextInput::make('slug')
                    ->label('Slug URL')
                    ->helperText('Kosongkan untuk dibuat otomatis dari judul.')
                    ->maxLength(200)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Str::slug($state) : null)
                    ->default(null),
                Select::make('type')
                    ->label('Tipe Balap')
                    ->options([
                        Event::TYPE_GRAN_FONDO => 'Gran Fondo',
                        Event::TYPE_MEDIO_FONDO => 'Medio Fondo',
                        Event::TYPE_FUN_RIDE => 'Fun Ride',
                        Event::TYPE_CLIMB => 'KOM Climb',
                    ])
                    ->default(Event::TYPE_GRAN_FONDO)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        Event::STATUS_DRAFT => 'Draft',
                        Event::STATUS_PUBLISHED => 'Published',
                        Event::STATUS_COMPLETED => 'Selesai',
                        Event::STATUS_CANCELLED => 'Dibatalkan',
                    ])
                    ->default(Event::STATUS_DRAFT)
                    ->required(),
                Select::make('user_id')
                    ->label('Penyelenggara')
                    ->options(
                        User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_PENYELENGGARA])
                            ->pluck('name', 'id')
                    )
                    ->default(fn (): ?int => Auth::id())
                    ->visible(fn (): bool => (bool) Auth::user()?->isAdmin())
                    ->required(),
                DatePicker::make('event_date')
                    ->label('Tanggal Race Day')
                    ->required(),
                TimePicker::make('start_time')
                    ->label('Waktu Start')
                    ->default('05:45')
                    ->required(),
                TextInput::make('location_name')
                    ->label('Lokasi (Kota, Provinsi)')
                    ->maxLength(200)
                    ->required(),
                TextInput::make('youtube_video_id')
                    ->label('YouTube Video ID')
                    ->maxLength(50)
                    ->nullable()
                    ->helperText('Contoh: dQw4w9WgXcQ'),
                FileUpload::make('gpx_file_path')
                    ->label('File Rute GPX')
                    ->directory('gpx')
                    ->disk('public')
                    ->nullable()
                    ->helperText('Upload file .gpx rute balap (opsional).'),
                FileUpload::make('map_image_path')
                    ->label('Foto Peta Sirkuit')
                    ->directory('event-maps')
                    ->disk('public')
                    ->image()
                    ->nullable()
                    ->helperText('Gambar peta rute yang tampil di halaman detail event. Kosongkan untuk memakai peta Google Maps.'),
                RichEditor::make('description')
                    ->label('Deskripsi Rute')
                    ->columnSpan(2)
                    ->required(),
                Repeater::make('categories')
                    ->label('Kategori Tiket')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->maxLength(100)
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode BIB')
                            ->maxLength(10)
                            ->helperText('GF, MF, FR, KOM')
                            ->required(),
                        TextInput::make('distance_km')
                            ->label('Jarak (KM)')
                            ->numeric()
                            ->required(),
                        TextInput::make('elevation_gain_m')
                            ->label('Elevasi (+m)')
                            ->numeric()
                            ->required(),
                        TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('quota')
                            ->label('Kuota')
                            ->numeric()
                            ->required(),
                        TextInput::make('registered_count')
                            ->label('Terdaftar')
                            ->numeric()
                            ->default(0),
                        TextInput::make('wave_start_time')
                            ->label('Wave Start')
                            ->maxLength(50)
                            ->placeholder('Wave 1 - 05:45 WIB')
                            ->required(),
                        TextInput::make('cut_off_hours')
                            ->label('Cut-off (Jam)')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->required()
                    ->collapsible()
                    ->columnSpan(2),
                Repeater::make('photos')
                    ->label('Galeri Foto')
                    ->relationship()
                    ->orderColumn('sort_order')
                    ->schema([
                        FileUpload::make('photo_path')
                            ->label('Foto')
                            ->image()
                            ->directory('event-photos')
                            ->disk('public')
                            ->required(),
                    ])
                    ->minItems(0)
                    ->defaultItems(0)
                    ->collapsible()
                    ->columnSpan(2),
            ]);
    }
}
