<?php

namespace App\Filament\Resources\HelpContents\Pages;

use App\Filament\Resources\HelpContents\HelpContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHelpContents extends ListRecords
{
    protected static string $resource = HelpContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
