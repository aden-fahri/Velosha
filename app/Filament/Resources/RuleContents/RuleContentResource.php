<?php

namespace App\Filament\Resources\RuleContents;

use App\Filament\Resources\RuleContents\Pages\CreateRuleContent;
use App\Filament\Resources\RuleContents\Pages\EditRuleContent;
use App\Filament\Resources\RuleContents\Pages\ListRuleContents;
use App\Filament\Resources\RuleContents\Schemas\RuleContentForm;
use App\Models\RuleContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RuleContentResource extends Resource
{
    protected static ?string $model = RuleContent::class;

    protected static ?string $navigationLabel = 'Rules Page';

    protected static ?string $modelLabel = 'Konten Aturan';

    protected static ?string $pluralModelLabel = 'Konten Aturan';

    protected static ?string $recordTitleAttribute = 'header_title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return RuleContentForm::configure($schema);
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
        return (bool) Auth::user()?->isAdmin() && ! RuleContent::query()->exists();
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
            'index' => ListRuleContents::route('/'),
            'create' => CreateRuleContent::route('/create'),
            'edit' => EditRuleContent::route('/{record}/edit'),
        ];
    }
}
