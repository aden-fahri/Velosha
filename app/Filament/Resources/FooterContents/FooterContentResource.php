<?php

namespace App\Filament\Resources\FooterContents;

use App\Filament\Resources\FooterContents\Pages\CreateFooterContent;
use App\Filament\Resources\FooterContents\Pages\EditFooterContent;
use App\Filament\Resources\FooterContents\Pages\ListFooterContents;
use App\Filament\Resources\FooterContents\Schemas\FooterContentForm;
use App\Models\FooterContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FooterContentResource extends Resource
{
    protected static ?string $model = FooterContent::class;

    protected static ?string $navigationLabel = 'Footer';

    protected static ?string $modelLabel = 'Konten Footer';

    protected static ?string $pluralModelLabel = 'Konten Footer';

    protected static ?string $recordTitleAttribute = 'copyright';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return FooterContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('copyright')
                    ->label('Subfooter')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('brand_description')
                    ->label('Deskripsi Brand')
                    ->limit(50)
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
        return (bool) Auth::user()?->isAdmin() && ! FooterContent::query()->exists();
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
            'index' => ListFooterContents::route('/'),
            'create' => CreateFooterContent::route('/create'),
            'edit' => EditFooterContent::route('/{record}/edit'),
        ];
    }
}
