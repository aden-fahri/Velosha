<?php

namespace App\Filament\Resources\TicketPackageContents;

use App\Filament\Resources\TicketPackageContents\Pages\CreateTicketPackageContent;
use App\Filament\Resources\TicketPackageContents\Pages\EditTicketPackageContent;
use App\Filament\Resources\TicketPackageContents\Pages\ListTicketPackageContents;
use App\Filament\Resources\TicketPackageContents\Schemas\TicketPackageContentForm;
use App\Models\TicketPackageContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketPackageContentResource extends Resource
{
    protected static ?string $model = TicketPackageContent::class;

    protected static ?string $navigationLabel = 'Ticket Package';

    protected static ?string $modelLabel = 'Isi Paket Tiket';

    protected static ?string $pluralModelLabel = 'Isi Paket Tiket';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return TicketPackageContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('eyebrow')
                    ->label('Label')
                    ->placeholder('—'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) Auth::user()?->isAdmin();
    }

    public static function canViewAny(): bool
    {
        return (bool) Auth::user()?->isAdmin();
    }

    public static function canCreate(): bool
    {
        return (bool) Auth::user()?->isAdmin() && ! TicketPackageContent::query()->exists();
    }

    public static function canEdit($record): bool
    {
        return (bool) Auth::user()?->isAdmin();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTicketPackageContents::route('/'),
            'create' => CreateTicketPackageContent::route('/create'),
            'edit' => EditTicketPackageContent::route('/{record}/edit'),
        ];
    }
}
