<?php

namespace App\Filament\Resources\AboutContents\Schemas;

use App\Models\AboutContent;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AboutContentForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaults = AboutContent::defaults();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Header Halaman')
                    ->description('Judul banner atas halaman Tentang.')
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
                Section::make('Manifesto')
                    ->schema([
                        TextInput::make('manifesto_title')
                            ->label('Judul Manifesto')
                            ->default($defaults['manifesto_title'])
                            ->required()
                            ->maxLength(200),
                        TextArea::make('manifesto_body')
                            ->label('Isi Manifesto')
                            ->default($defaults['manifesto_body'])
                            ->rows(7)
                            ->helperText('Pisahkan tiap paragraf dengan satu baris kosong.')
                            ->required(),
                    ]),
                Section::make('Nilai Utama')
                    ->schema([
                        Repeater::make('value_items')
                            ->label('Daftar Nilai')
                            ->default($defaults['value_items'])
                            ->schema([
                                TextInput::make('icon')
                                    ->label('Ikon Lucide')
                                    ->placeholder('shield-check')
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
                            ->addActionLabel('Tambah nilai')
                            ->collapsible(),
                    ]),
                Section::make('CTA Penutup')
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
