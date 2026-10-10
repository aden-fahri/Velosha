<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Support\PanelScope;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class VeloshaStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -10;

    protected static bool $isLazy = false;

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $lunasStatuses = [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN];

        return [
            Stat::make('Event Terbit', number_format($this->jumlahEvent()))
                ->description('kalender balap aktif')
                ->icon(Heroicon::CalendarDays),
            Stat::make('Peserta', number_format($this->jumlahPeserta()))
                ->description('akun rider terdaftar')
                ->icon(Heroicon::Users),
            Stat::make('Total Pendaftar', number_format(PanelScope::registrations(Registration::query())->count()))
                ->description('seluruh status pendaftaran')
                ->icon(Heroicon::ClipboardDocumentList),
            Stat::make('Lunas', number_format(PanelScope::registrations(Registration::query())->whereIn('status', $lunasStatuses)->count()))
                ->description('BIB & tiket QR terbit')
                ->color('success')
                ->icon(Heroicon::CheckBadge),
            Stat::make('Check-in RPC', number_format(PanelScope::registrations(Registration::query())->where('status', Registration::STATUS_CHECKED_IN)->count()))
                ->description('sudah ambil race pack')
                ->color('info')
                ->icon(Heroicon::UserGroup),
            Stat::make('Menunggu Bayar', number_format(PanelScope::registrations(Registration::query())->where('status', Registration::STATUS_PENDING)->count()))
                ->description('perlu tindak lanjut')
                ->color('warning')
                ->icon(Heroicon::Clock),
            Stat::make('Estimasi Pendapatan', 'Rp '.number_format($this->pendapatanLunas($lunasStatuses), 0, ',', '.'))
                ->description('akumulasi pendaftar lunas')
                ->icon(Heroicon::CurrencyDollar),
        ];
    }

    protected function jumlahEvent(): int
    {
        return PanelScope::events(Event::query())->published()->count();
    }

    protected function jumlahPeserta(): int
    {
        if (PanelScope::isAdmin()) {
            return User::where('role', User::ROLE_PESERTA)->count();
        }

        return PanelScope::registrations(Registration::query())
            ->distinct()
            ->count('user_id');
    }

    /**
     * @param  array<int, string>  $lunasStatuses
     */
    protected function pendapatanLunas(array $lunasStatuses): float
    {
        return (float) Registration::query()
            ->join('event_categories', 'event_categories.id', '=', 'registrations.event_category_id')
            ->join('events', 'events.id', '=', 'event_categories.event_id')
            ->when(! PanelScope::isAdmin(), fn ($query) => $query->where('events.user_id', Auth::id()))
            ->whereIn('registrations.status', $lunasStatuses)
            ->sum('event_categories.price');
    }
}
