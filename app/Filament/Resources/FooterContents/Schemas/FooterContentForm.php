<?php

namespace App\Filament\Resources\FooterContents\Schemas;

use App\Models\FooterContent;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FooterContentForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaults = FooterContent::defaults();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Brand')
                    ->columns(2)
                    ->schema([
                        TextArea::make('brand_description')
                            ->label('Deskripsi Brand')
                            ->default($defaults['brand_description'])
                            ->rows(2)
                            ->required()
                            ->columnSpan(2),
                    ]),
                Section::make('Kolom Kategori')
                    ->columns(2)
                    ->schema([
                        TextInput::make('kategori_title')
                            ->label('Judul Kolom')
                            ->default($defaults['kategori_title'])
                            ->required()
                            ->maxLength(60),
                        Repeater::make('kategori_links')
                            ->label('Tautan Kategori')
                            ->default($defaults['kategori_links'])
                            ->schema([
                                TextInput::make('label')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(60),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->placeholder('/events')
                                    ->maxLength(300),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Tambah tautan')
                            ->collapsible(),
                    ]),
                Section::make('Kolom Bantuan')
                    ->description('URL /dashboard dan /admin otomatis hanya ditampilkan ke peran yang sesuai.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('bantuan_title')
                            ->label('Judul Kolom')
                            ->default($defaults['bantuan_title'])
                            ->required()
                            ->maxLength(60),
                        Repeater::make('bantuan_links')
                            ->label('Tautan Bantuan')
                            ->default($defaults['bantuan_links'])
                            ->schema([
                                TextInput::make('label')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(60),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->placeholder('/events')
                                    ->maxLength(300),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Tambah tautan')
                            ->collapsible(),
                    ]),
                Section::make('Metode Pembayaran')
                    ->columns(2)
                    ->schema([
                        TextInput::make('payment_title')
                            ->label('Judul Kolom')
                            ->default($defaults['payment_title'])
                            ->required()
                            ->maxLength(60),
                        Repeater::make('payment_methods')
                            ->label('Daftar Metode')
                            ->default($defaults['payment_methods'])
                            ->schema([
                                TextInput::make('label')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(60),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Tambah metode')
                            ->collapsible(),
                    ]),
                Section::make('Subfooter')
                    ->columns(2)
                    ->schema([
                        TextInput::make('copyright')
                            ->label('Teks Hak Cipta')
                            ->default($defaults['copyright'])
                            ->required()
                            ->maxLength(200)
                            ->columnSpan(2),
                        Repeater::make('bottom_links')
                            ->label('Tautan Bawah')
                            ->default($defaults['bottom_links'])
                            ->schema([
                                TextInput::make('label')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(60),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->placeholder('/rule')
                                    ->maxLength(300),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Tambah tautan')
                            ->collapsible(),
                    ]),
            ]);
    }
}
