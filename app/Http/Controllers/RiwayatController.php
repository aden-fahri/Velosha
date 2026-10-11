<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Response;

class RiwayatController extends Controller
{
    /**
     * Arsip riwayat balap + riwayat pendaftaran milik pengguna login.
     */
    public function index(): Response
    {
        $today = now()->toDateString();

        $archive = Event::query()
            ->where('status', '!=', Event::STATUS_DRAFT)
            ->where('status', '!=', Event::STATUS_CANCELLED)
            ->where(function ($query) use ($today) {
                $query->whereDate('event_date', '<', $today)
                    ->orWhere('status', Event::STATUS_COMPLETED);
            })
            ->with('categories.registrations')
            ->orderByDesc('event_date')
            ->get();

        return response()->view('pages.riwayat', [
            'archive' => $archive,
            'meta' => $archive->count().' Race Selesai · Musim 2026–2027',
        ]);
    }
}
