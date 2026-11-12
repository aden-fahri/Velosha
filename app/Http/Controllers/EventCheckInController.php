<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Registration;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventCheckInController extends Controller
{
    /**
     * Tampilkan antarmuka Gate Scanner hari-H balap.
     */
    public function index(Organization $organization, Event $event): View
    {
        $this->authorizeAccess($organization, $event);

        $event->load('categories');

        $totalRegistrations = Registration::whereHas('category', fn ($q) => $q->where('event_id', $event->id))
            ->whereIn('status', [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN])
            ->count();

        $checkedInCount = Registration::whereHas('category', fn ($q) => $q->where('event_id', $event->id))
            ->where('status', Registration::STATUS_CHECKED_IN)
            ->count();

        return view('pages.organizations.events.scanner', [
            'organization' => $organization,
            'event' => $event,
            'totalRegistrations' => $totalRegistrations,
            'checkedInCount' => $checkedInCount,
        ]);
    }

    /**
     * Verifikasi & konfirmasi check-in tiket via pemindaian QR atau nomor BIB.
     */
    public function verify(Request $request, Organization $organization, Event $event): JsonResponse
    {
        $this->authorizeAccess($organization, $event);

        $code = trim((string) $request->input('code'));
        if ($code === '') {
            return response()->json([
                'success' => false,
                'status' => 'empty',
                'message' => 'Kode QR atau nomor BIB tidak boleh kosong.',
            ], 422);
        }

        // 1. Cari pendaftaran berdasarkan BIB atau qr_code_data tiket
        $registration = Registration::whereHas('category', fn ($q) => $q->where('event_id', $event->id))
            ->where(function ($q) use ($code) {
                $q->where('bib_number', $code)
                  ->orWhereHas('ticket', fn ($tq) => $tq->where('qr_code_data', $code));
            })
            ->with(['category', 'ticket', 'user'])
            ->first();

        // 2. Fallback jika QR code membawa string penuh VELOSHA-VERIFY-...
        if (! $registration && str_contains($code, 'VELOSHA-VERIFY-')) {
            $parts = explode('-', $code);
            $possibleBib = end($parts);
            $registration = Registration::whereHas('category', fn ($q) => $q->where('event_id', $event->id))
                ->where('bib_number', $possibleBib)
                ->with(['category', 'ticket', 'user'])
                ->first();
        }

        if (! $registration) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => "Tiket atau BIB '{$code}' tidak ditemukan pada event ini.",
            ], 404);
        }

        if ($registration->status === Registration::STATUS_CANCELLED) {
            return response()->json([
                'success' => false,
                'status' => 'cancelled',
                'message' => "Pendaftaran BIB {$registration->bib_number} telah DIBATALKAN.",
                'data' => [
                    'bib_number' => $registration->bib_number,
                    'participant_name' => $registration->participant_name,
                ],
            ], 400);
        }

        if ($registration->status === Registration::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'status' => 'unpaid',
                'message' => "Pendaftaran BIB {$registration->bib_number} BELUM LUNAS.",
                'data' => [
                    'bib_number' => $registration->bib_number,
                    'participant_name' => $registration->participant_name,
                ],
            ], 400);
        }

        $ticket = $registration->ticket;

        // Cek apakah tiket sudah pernah di-check in sebelumnya
        if ($registration->status === Registration::STATUS_CHECKED_IN || ($ticket && $ticket->is_checked_in)) {
            $time = $ticket?->checked_in_at?->translatedFormat('d M Y, H:i') ?? $registration->updated_at?->translatedFormat('d M Y, H:i');

            return response()->json([
                'success' => false,
                'status' => 'already_checked_in',
                'message' => "PERINGATAN: Tiket ini SUDAH CHECK-IN pada {$time} WIB!",
                'data' => [
                    'bib_number' => $registration->bib_number,
                    'participant_name' => $registration->participant_name,
                    'category_name' => $registration->category?->name,
                    'blood_type' => $registration->blood_type ?? '-',
                    'club' => $registration->club ?: 'Independen',
                    'emergency_contact' => $registration->emergency_contact_name . ' (' . ($registration->emergency_contact_phone ?? '-') . ')',
                    'checked_in_at' => $time,
                ],
            ], 409);
        }

        // Lakukan eksekusi check-in jika diminta atau mode auto-confirm
        $confirm = $request->boolean('confirm', true);

        if ($confirm) {
            $registration->update(['status' => Registration::STATUS_CHECKED_IN]);
            if ($ticket) {
                $ticket->update([
                    'is_checked_in' => true,
                    'checked_in_at' => now(),
                    'checked_in_by_admin' => Auth::id(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => 'checked_in_now',
            'message' => "BERHASIL CHECK-IN! Silakan serahkan nomor BIB & racepack.",
            'data' => [
                'bib_number' => $registration->bib_number,
                'participant_name' => $registration->participant_name,
                'category_name' => $registration->category?->name,
                'blood_type' => $registration->blood_type ?? '-',
                'club' => $registration->club ?: 'Independen',
                'emergency_contact' => $registration->emergency_contact_name . ' (' . ($registration->emergency_contact_phone ?? '-') . ')',
                'checked_in_at' => now()->translatedFormat('H:i:s'),
            ],
        ]);
    }

    /**
     * Pastikan pengguna adalah admin platform atau anggota komunitas pemilik event.
     */
    protected function authorizeAccess(Organization $organization, Event $event): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        if ($user->isAdmin() || $user->isOperator()) {
            return;
        }

        $isMember = $organization->users()->where('users.id', $user->id)->exists();
        if (! $isMember || (int) $event->organization_id !== (int) $organization->id) {
            abort(403, 'Akses Gate Scanner hanya untuk panitia penyelenggara event ini.');
        }
    }
}
