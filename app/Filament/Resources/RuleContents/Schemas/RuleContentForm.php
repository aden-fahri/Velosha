<?php

namespace App\Filament\Resources\RuleContents\Schemas;

use App\Models\RuleContent;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RuleContentForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaults = RuleContent::defaults();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Header Halaman')
                    ->description('Judul banner atas halaman Aturan.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('header_eyebrow')
                            ->label('Label Kecil')
                            ->default($defaults['header_eyebrow'])
                            ->maxLength(100),
                        TextInput::make('header_meta')
                            ->label('Meta Kanan')
                            ->default($defaults['header_meta'])
                            ->maxLength(100),
                        TextInput::make('header_title')
                            ->label('Judul')
                            ->default($defaults['header_title'])
                            ->required()
                            ->maxLength(200)
                            ->columnSpan(2),
                        TextArea::make('header_description')
                            ->label('Deskripsi')
                            ->default($defaults['header_description'])
                            ->rows(2)
                            ->columnSpan(2),
                    ]),
                Section::make('Kelompok Aturan')
                    ->description('Setiap kelompok berisi daftar butir aturan dengan statusnya.')
                    ->schema([
                        Repeater::make('groups')
                            ->label('Daftar Kelompok')
                            ->default($defaults['groups'])
                            ->grid(2)
                            ->extraAttributes(['class' => 'repeater-add-grid'])
                            ->schema([
                                TextInput::make('icon')
                                    ->label('Ikon Lucide')
                                    ->placeholder('bike')
                                    ->maxLength(50),
                                TextInput::make('title')
                                    ->label('Judul Kelompok')
                                    ->required()
                                    ->maxLength(150),
                                Repeater::make('items')
                                    ->label('Butir Aturan')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Nama Butir')
                                            ->required()
                                            ->maxLength(150),
                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'allowed' => 'Diizinkan',
                                                'mandatory' => 'Wajib',
                                                'forbidden' => 'Dilarang',
                                                'note' => 'Informasi',
                                            ])
                                            ->default('mandatory')
                                            ->required(),
                                        TextArea::make('text')
                                            ->label('Keterangan')
                                            ->rows(3)
                                            ->required()
                                            ->columnSpan(2),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->addActionLabel('Tambah butir')
                                    ->collapsible()
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Tambah Kelompok Aturan Baru')
                            ->collapsible(),
                    ]),
                Section::make('Sanksi Pelanggaran')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('sanksi_items')
                            ->label('Daftar Sanksi')
                            ->default($defaults['sanksi_items'])
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Sanksi')
                                    ->required()
                                    ->maxLength(100),
                                Select::make('tone')
                                    ->label('Warna')
                                    ->options([
                                        'neutral' => 'Ringan (abu-abu)',
                                        'race' => 'Sedang (race)',
                                        'danger' => 'Berat (merah)',
                                    ])
                                    ->default('neutral')
                                    ->required(),
                                TextArea::make('text')
                                    ->label('Keterangan')
                                    ->rows(3)
                                    ->required()
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Tambah sanksi')
                            ->collapsible(),
                        TextArea::make('sanksi_note')
                            ->label('Catatan Penutup')
                            ->default($defaults['sanksi_note'])
                            ->rows(2),
                    ]),
                Section::make('CTA Penutup')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextInput::make('cta_title')
                            ->label('Judul CTA')
                            ->default($defaults['cta_title'])
                            ->required()
                            ->maxLength(200),
                        TextInput::make('cta_label')
                            ->label('Tombol')
                            ->default($defaults['cta_label'])
                            ->required()
                            ->maxLength(100),
                        TextArea::make('cta_desc')
                            ->label('Deskripsi CTA')
                            ->default($defaults['cta_desc'])
                            ->rows(2)
                            ->columnSpan(2),
                        TextInput::make('cta_url')
                            ->label('URL Tombol')
                            ->default($defaults['cta_url'])
                            ->required()
                            ->placeholder('/events')
                            ->maxLength(300)
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
