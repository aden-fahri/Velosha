<?php

namespace App\Filament\Resources\TicketPackageContents\Schemas;

use App\Models\TicketPackageContent;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketPackageContentForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaults = TicketPackageContent::defaults();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Header Bagian')
                    ->description('Judul bagian "Yang Kamu Dapat dari Satu Tiket" di halaman utama.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('eyebrow')
                            ->label('Label Kecil')
                            ->default($defaults['eyebrow'])
                            ->required()
                            ->maxLength(100),
                        TextInput::make('title')
                            ->label('Judul')
                            ->default($defaults['title'])
                            ->required()
                            ->maxLength(200)
                            ->columnSpan(2),
                        TextArea::make('description')
                            ->label('Deskripsi')
                            ->default($defaults['description'])
                            ->rows(2)
                            ->columnSpan(2),
                    ]),
                Section::make('Kartu Fitur')
                    ->description('Empat kartu ikon besar yang mewakili isi paket tiket.')
                    ->schema([
                        Repeater::make('features')
                            ->label('Daftar Fitur')
                            ->default($defaults['features'])
                            ->schema([
                                TextInput::make('icon')
                                    ->label('Ikon Lucide')
                                    ->placeholder('cpu')
                                    ->maxLength(50),
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('desc')
                                    ->label('Deskripsi')
                                    ->required()
                                    ->maxLength(300)
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Tambah kartu')
                            ->collapsible(),
                    ]),
                Section::make('Benefit Lain')
                    ->schema([
                        TextInput::make('benefits_title')
                            ->label('Judul Daftar')
                            ->default($defaults['benefits_title'])
                            ->required()
                            ->maxLength(150),
                        Repeater::make('benefits')
                            ->label('Daftar Benefit')
                            ->default($defaults['benefits'])
                            ->schema([
                                TextInput::make('text')
                                    ->label('Teks')
                                    ->required()
                                    ->maxLength(300),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                            ->addActionLabel('Tambah benefit')
                            ->collapsible(),
                    ]),
            ]);
    }
}
