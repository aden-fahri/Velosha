<?php

namespace App\Filament\Resources\HelpContents;

use App\Filament\Resources\HelpContents\Pages\CreateHelpContent;
use App\Filament\Resources\HelpContents\Pages\EditHelpContent;
use App\Filament\Resources\HelpContents\Pages\ListHelpContents;
use App\Filament\Resources\HelpContents\Schemas\HelpContentForm;
use App\Models\HelpContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class HelpContentResource extends Resource
{
    protected static ?string $model = HelpContent::class;

    protected static ?string $navigationLabel = 'Help Page';

    protected static ?string $modelLabel = 'Konten Bantuan';

    protected static ?string $pluralModelLabel = 'Konten Bantuan';

    protected static ?string $recordTitleAttribute = 'header_title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return HelpContentForm::configure($schema);
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
        return (bool) Auth::user()?->isAdmin() && ! HelpContent::query()->exists();
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
            'index' => ListHelpContents::route('/'),
            'create' => CreateHelpContent::route('/create'),
            'edit' => EditHelpContent::route('/{record}/edit'),
        ];
    }
}
