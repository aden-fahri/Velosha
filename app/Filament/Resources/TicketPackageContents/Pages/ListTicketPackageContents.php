<?php

namespace App\Filament\Resources\TicketPackageContents\Pages;

use App\Filament\Resources\TicketPackageContents\TicketPackageContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTicketPackageContents extends ListRecords
{
    protected static string $resource = TicketPackageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
