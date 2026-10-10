<?php

namespace App\Support;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Pembatas cakupan data untuk halaman panel (Filament) sesuai peran.
 * Admin melihat seluruh data; penyelenggara hanya event miliknya sendiri.
 */
class PanelScope
{
    public static function isAdmin(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Batasi query event pada pemiliknya bila bukan admin.
     *
     * @param  Builder<Event>  $query
     * @return Builder<Event>
     */
    public static function events(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user instanceof User && ! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Batasi query pendaftaran pada event milik pemiliknya bila bukan admin.
     *
     * @param  Builder<Registration>  $query
     * @return Builder<Registration>
     */
    public static function registrations(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user instanceof User && ! $user->isAdmin()) {
            $query->whereHas('category.event', function (Builder $eventQuery) use ($user): void {
                $eventQuery->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
