<?php

namespace App\Filament\Resources\PayoutRequests;

use App\Filament\Resources\PayoutRequests\Pages\ListPayoutRequests;
use App\Filament\Resources\PayoutRequests\Tables\PayoutRequestsTable;
use App\Models\PayoutRequest;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PayoutRequestResource extends Resource
{
    // ponytail: dinonaktifkan sementara dari navigasi sesuai permintaan pengguna
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $model = PayoutRequest::class;

    protected static ?string $navigationLabel = 'Pencairan Dana (Payout)';

    protected static string | \UnitEnum | null $navigationGroup = 'Operasional Balap';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Pencairan Dana';

    protected static ?string $pluralModelLabel = 'Pencairan Dana (Payout)';

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function table(Table $table): Table
    {
        return PayoutRequestsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user instanceof User && ! $user->isAdmin()) {
            $orgIds = $user->organizations()->pluck('organizations.id');
            $query->whereIn('payout_requests.organization_id', $orgIds);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayoutRequests::route('/'),
        ];
    }
}
