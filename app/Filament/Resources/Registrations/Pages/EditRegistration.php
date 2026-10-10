<?php

namespace App\Filament\Resources\Registrations\Pages;

use App\Filament\Resources\Registrations\RegistrationResource;
use Filament\Resources\Pages\EditRecord;

class EditRegistration extends EditRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function afterSave(): void
    {
        /** @var \App\Models\Registration $record */
        $record = $this->record;

        if ($record->isPaid()) {
            $category = $record->category;
            if (empty($record->bib_number) && $category) {
                $record->bib_number = \App\Models\Registration::generateBibNumber($category);
                $record->saveQuietly();
                $category->increment('registered_count');
            }
            \App\Models\Ticket::issueFor($record);
        }
    }
}
