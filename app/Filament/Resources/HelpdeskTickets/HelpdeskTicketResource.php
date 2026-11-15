<?php

namespace App\Filament\Resources\HelpdeskTickets;

use App\Filament\Resources\HelpdeskTickets\Pages\ListHelpdeskTickets;
use App\Filament\Resources\HelpdeskTickets\Pages\EditHelpdeskTicket;
use App\Models\HelpdeskTicket;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HelpdeskTicketResource extends Resource
{
    protected static ?string $model = HelpdeskTicket::class;

    protected static ?string $navigationLabel = 'Antrian Tiket';

    protected static string | \UnitEnum | null $navigationGroup = 'Helpdesk';

    protected static ?string $modelLabel = 'Tiket Bantuan';

    protected static ?string $pluralModelLabel = 'Antrian Tiket';

    protected static ?string $recordTitleAttribute = 'subject';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->label('Subjek Tiket')
                    ->disabled(),
                Select::make('category')
                    ->label('Kategori')
                    ->options(\App\Enums\Helpdesk\TicketCategory::class)
                    ->disabled(),
                Select::make('status')
                    ->label('Status Tiket')
                    ->options(\App\Enums\TicketStatus::class)
                    ->required(),
                Select::make('priority')
                    ->label('Prioritas')
                    ->options(\App\Enums\TicketPriority::class)
                    ->required(),
                Select::make('operator_id')
                    ->label('Petugas Operator')
                    ->relationship('operator', 'name'),
                Textarea::make('description')
                    ->label('Deskripsi Aduan')
                    ->disabled()
                    ->columnSpanFull(),
                Textarea::make('solution_summary')
                    ->label('Ringkasan Solusi Pengelola')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#ID')->sortable(),
                TextColumn::make('reporter.name')->label('Pelapor')->searchable(),
                TextColumn::make('subject')->label('Subjek Tiket')->searchable()->limit(40),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('priority')->label('Prioritas')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('operator.name')->label('Operator')->placeholder('Belum Ditugaskan'),
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHelpdeskTickets::route('/'),
            'edit' => EditHelpdeskTicket::route('/{record}/edit'),
        ];
    }
}
