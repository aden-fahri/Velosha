<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    /**
     * Halaman e-ticket resmi: tampilkan pendaftaran milik user
     * (atau pendaftaran terbaru bila tidak dispesifikasikan).
     * Akses: pemilik pendaftaran, admin, atau penyelenggara pemilik event.
     */
    public function show(?Registration $registration = null): Response
    {
        $user = auth()->user();

        if ($registration instanceof Registration) {
            abort_unless(
                $registration->user_id === $user->id
                || $user->isAdmin()
                || ($user->isPenyelenggara() && $registration->category?->event?->user_id === $user->id),
                403
            );
            $registration->loadMissing(['category.event.photos', 'ticket', 'user']);
        } else {
            $registration = $user->registrations()->with(['category.event.photos', 'ticket', 'user'])->latest()->first();
        }

        return response()->view('pages.ticket', ['registration' => $registration]);
    }
}
