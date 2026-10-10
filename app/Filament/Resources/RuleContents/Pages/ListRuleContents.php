<?php

namespace App\Filament\Resources\RuleContents\Pages;

use App\Filament\Resources\RuleContents\RuleContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuleContents extends ListRecords
{
    protected static string $resource = RuleContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
