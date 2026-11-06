<?php

namespace App\Http\Controllers;

use App\Actions\CompleteRegistrationAction;
use App\Models\Payment;
use App\Models\Registration;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
        protected CompleteRegistrationAction $completeAction
    ) {}

    /**
     * Tangani notifikasi webhook resmi dari Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');
        $transactionStatus = (string) $request->input('transaction_status');
        $fraudStatus = (string) $request->input('fraud_status');

        if (empty($orderId) || empty($transactionStatus)) {
            return response()->json(['message' => 'Payload webhook tidak lengkap.'], 400);
        }

        // 1. Verifikasi keamanan checksum hash
        if (! empty($signatureKey) && ! $this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning("Midtrans Webhook: Signature tidak cocok untuk order {$orderId}");
            return response()->json(['message' => 'Security hash tidak valid.'], 403);
        }

        // 2. Cari data Payment berdasarkan order_id
        $payment = Payment::where('order_id', $orderId)->first();

        if (! $payment) {
            Log::warning("Midtrans Webhook: Order ID {$orderId} tidak ditemukan.");
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        // 3. Validasi kesesuaian jumlah bayar (gross amount)
        if (! empty($grossAmount) && (int) (float) $grossAmount !== (int) $payment->amount) {
            Log::warning("Midtrans Webhook: Jumlah bayar tidak cocok untuk order {$orderId}. Terdaftar: {$payment->amount}, Diterima: {$grossAmount}");
            return response()->json(['message' => 'Gross amount tidak sesuai pesanan.'], 400);
        }

        // 4. Idempotensi: jika sudah lunas, jangan proses ulang
        if ($payment->isSettled()) {
            return response()->json(['message' => 'Pembayaran sudah berstatus lunas sebelumnya.'], 200);
        }

        // 5. Proses status transaksi
        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $payment->update([
                'status' => Payment::STATUS_SETTLEMENT,
                'payment_type' => $request->input('payment_type', $payment->payment_type),
                'paid_at' => now(),
                'payload' => $request->all(),
            ]);

            // Terbitkan BIB resmi & e-ticket QR atomik
            if ($payment->registration) {
                try {
                    $this->completeAction->execute($payment->registration);
                } catch (\RuntimeException $e) {
                    Log::error("Midtrans Webhook: Gagal menerbitkan tiket untuk order {$orderId}: " . $e->getMessage());
                    return response()->json([
                        'status' => 'FAILED',
                        'message' => 'Pendaftaran tidak dapat diselesaikan: ' . $e->getMessage(),
                    ], 200);
                }
            }

            Log::info("Midtrans Webhook: Pembayaran sukses untuk order {$orderId}");
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
            $payment->update([
                'status' => $transactionStatus === 'expire' ? Payment::STATUS_EXPIRE : Payment::STATUS_CANCEL,
                'payload' => $request->all(),
            ]);

            if ($payment->registration && ! $payment->registration->isPaid()) {
                $payment->registration->update([
                    'status' => Registration::STATUS_CANCELLED,
                ]);
            }

            Log::info("Midtrans Webhook: Pembayaran {$transactionStatus} untuk order {$orderId}");
        }

        return response()->json(['status' => 'OK', 'message' => 'Webhook berhasil diproses.'], 200);
    }
}

