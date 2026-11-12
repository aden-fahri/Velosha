<?php

namespace App\Http\Controllers;

use App\Actions\CompleteRegistrationAction;
use App\Models\Payment;
use App\Models\Registration;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    /**
     * Halaman e-ticket resmi: tampilkan pendaftaran milik user
     * (atau pendaftaran terbaru bila tidak dispesifikasikan).
     * Akses: pemilik pendaftaran, admin, atau penyelenggara pemilik event.
     */
    public function show(Request $request, ?Registration $registration = null, MidtransService $midtrans, CompleteRegistrationAction $completeAction): Response
    {
        $user = auth()->user();

        if ($registration instanceof Registration) {
            abort_unless(
                $registration->user_id === $user->id
                || $user->isAdmin()
                || ($user->isPenyelenggara() && $registration->category?->event?->user_id === $user->id),
                403
            );
            $registration->loadMissing(['category.event.photos', 'ticket', 'user', 'payment']);
        } elseif ($request->filled('order_id')) {
            $paymentByOrder = Payment::where('order_id', (string) $request->query('order_id'))->first();
            if ($paymentByOrder && $paymentByOrder->registration && ($paymentByOrder->registration->user_id === $user->id || $user->isAdmin())) {
                $registration = $paymentByOrder->registration;
                $registration->loadMissing(['category.event.photos', 'ticket', 'user', 'payment']);
            } else {
                $registration = $user->registrations()->with(['category.event.photos', 'ticket', 'user', 'payment'])->latest()->first();
            }
        } else {
            $registration = $user->registrations()->with(['category.event.photos', 'ticket', 'user', 'payment'])->latest()->first();
        }

        // Auto-sync status pembayaran tiket jika masih pending (berguna untuk lingkungan lokal & webhook tertunda)
        if ($registration instanceof Registration && ! $registration->isPaid() && $registration->payment) {
            $payment = $registration->payment;
            $isPaid = false;

            if ($midtrans->isConfigured()) {
                $status = $midtrans->checkStatus($payment->order_id);
                if ($status) {
                    $trxStatus = $status['transaction_status'] ?? '';
                    $fraudStatus = $status['fraud_status'] ?? '';
                    if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                        $isPaid = true;
                    }
                }
            }

            if (! $isPaid) {
                $qStatus = (string) $request->query('transaction_status');
                if (in_array($qStatus, ['settlement', 'capture', 'success'], true)) {
                    $isPaid = true;
                }
            }

            if ($isPaid) {
                $payment->update([
                    'status' => Payment::STATUS_SETTLEMENT,
                    'paid_at' => now(),
                ]);

                try {
                    $completeAction->execute($registration);
                    $registration->refresh()->loadMissing(['category.event.photos', 'ticket', 'user', 'payment']);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('TicketController auto-sync error: ' . $e->getMessage());
                }
            }
        }

        return response()->view('pages.ticket', ['registration' => $registration]);
    }

    /**
     * Halaman cetak / unduh resmi A4 E-Ticket PDF.
     */
    public function download(Request $request, ?Registration $registration = null): Response
    {
        $user = auth()->user();

        if ($registration instanceof Registration) {
            abort_unless(
                $registration->user_id === $user->id
                || $user->isAdmin()
                || ($user->isPenyelenggara() && $registration->category?->event?->user_id === $user->id),
                403
            );
            $registration->loadMissing(['category.event', 'ticket', 'user', 'payment']);
        } else {
            $registration = $user->registrations()
                ->whereIn('status', [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN])
                ->with(['category.event', 'ticket', 'user', 'payment'])
                ->latest()
                ->first();
        }

        abort_if(! $registration || ! $registration->isPaid(), 404, 'E-Ticket belum diterbitkan atau pembayaran belum lunas.');

        return response()->view('pages.ticket-pdf', ['registration' => $registration]);
    }
}
