<?php

namespace App\Filament\Resources\RaceBannerContents;

use App\Filament\Resources\RaceBannerContents\Pages\CreateRaceBannerContent;
use App\Filament\Resources\RaceBannerContents\Pages\EditRaceBannerContent;
use App\Filament\Resources\RaceBannerContents\Pages\ListRaceBannerContents;
use App\Filament\Resources\RaceBannerContents\Schemas\RaceBannerContentForm;
use App\Models\RaceBannerContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RaceBannerContentResource extends Resource
{
    protected static ?string $model = RaceBannerContent::class;

    protected static ?string $navigationLabel = 'Banner Race';

    protected static ?string $modelLabel = 'Banner Race';

    protected static ?string $pluralModelLabel = 'Banner Race';

    protected static ?string $recordTitleAttribute = 'image_path';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return RaceBannerContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Pratinjau')
                    ->disk('public')
                    ->limit(30),
                TextColumn::make('image_path')
                    ->label('Path File')
                    ->placeholder('Belum ada gambar (memakai placeholder)')
                    ->limit(50),
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
        return (bool) Auth::user()?->isAdmin() && ! RaceBannerContent::query()->exists();
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
            'index' => ListRaceBannerContents::route('/'),
            'create' => CreateRaceBannerContent::route('/create'),
            'edit' => EditRaceBannerContent::route('/{record}/edit'),
        ];
    }
}
