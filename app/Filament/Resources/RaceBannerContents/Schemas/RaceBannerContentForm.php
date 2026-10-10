<?php

namespace App\Filament\Resources\RaceBannerContents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RaceBannerContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Gambar Banner Race')
                    ->description('Banner besar di atas kartu race pada halaman utama (section Pendaftaran Terbuka & Mendatang).')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Gambar Banner')
                            ->image()
                            ->directory('race-banners')
                            ->disk('public')
                            ->nullable()
                            ->helperText('Rasio lebar ~6:1 (mis. 1216×192 px). Kosongkan untuk kembali memakai placeholder default.'),
                    ]),
            ]);
    }
}
