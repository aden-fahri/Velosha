<?php

namespace App\Filament\Resources\RaceBannerContents\Pages;

use App\Filament\Resources\RaceBannerContents\RaceBannerContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRaceBannerContents extends ListRecords
{
    protected static string $resource = RaceBannerContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
