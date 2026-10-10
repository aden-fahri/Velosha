<?php

namespace App\Filament\Resources\AboutContents;

use App\Filament\Resources\AboutContents\Pages\CreateAboutContent;
use App\Filament\Resources\AboutContents\Pages\EditAboutContent;
use App\Filament\Resources\AboutContents\Pages\ListAboutContents;
use App\Filament\Resources\AboutContents\Schemas\AboutContentForm;
use App\Models\AboutContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AboutContentResource extends Resource
{
    protected static ?string $model = AboutContent::class;

    protected static ?string $navigationLabel = 'About Page';

    protected static ?string $modelLabel = 'Konten Tentang';

    protected static ?string $pluralModelLabel = 'Konten Tentang';

    protected static ?string $recordTitleAttribute = 'header_title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AboutContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('header_title')
                    ->label('Judul Halaman')
                    ->searchable(),
                TextColumn::make('header_eyebrow')
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
        return (bool) Auth::user()?->isAdmin() && ! AboutContent::query()->exists();
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
            'index' => ListAboutContents::route('/'),
            'create' => CreateAboutContent::route('/create'),
            'edit' => EditAboutContent::route('/{record}/edit'),
        ];
    }
}
