<?php

namespace App\Filament\Resources\HelpContents\Schemas;

use App\Models\HelpContent;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HelpContentForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaults = HelpContent::defaults();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Header Halaman')
                    ->description('Judul banner atas halaman Help Center.')
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
                Section::make('Pintasan Kontak')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('contacts')
                            ->label('Daftar Kontak')
                            ->default($defaults['contacts'])
                            ->schema([
                                TextInput::make('icon')
                                    ->label('Ikon Lucide')
                                    ->placeholder('message-circle')
                                    ->maxLength(50),
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('info')
                                    ->label('Keterangan')
                                    ->required()
                                    ->maxLength(150),
                                TextInput::make('value')
                                    ->label('Nilai / Kontak')
                                    ->required()
                                    ->maxLength(150),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Tambah kontak')
                            ->collapsible(),
                    ]),
                Section::make('FAQ per Kategori')
                    ->description('Grup pertanyaan yang tampil sebagai daftar accordion.')
                    ->schema([
                        Repeater::make('faq_groups')
                            ->label('Daftar Grup FAQ')
                            ->default($defaults['faq_groups'])
                            ->grid(2)
                            ->extraAttributes(['class' => 'repeater-add-grid'])
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Grup')
                                    ->required()
                                    ->maxLength(150)
                                    ->columnSpan(2),
                                Repeater::make('items')
                                    ->label('Pertanyaan')
                                    ->schema([
                                        TextInput::make('q')
                                            ->label('Pertanyaan')
                                            ->required()
                                            ->maxLength(300),
                                        TextArea::make('a')
                                            ->label('Jawaban')
                                            ->rows(3)
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['q'] ?? null)
                                    ->addActionLabel('Tambah pertanyaan')
                                    ->collapsible()
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Tambah Grup FAQ Baru')
                            ->collapsible(),
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
