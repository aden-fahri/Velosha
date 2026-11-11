<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransService
{
    /**
     * Cek apakah kredensial Midtrans sudah diisi di .env.
     */
    public function isConfigured(): bool
    {
        return ! empty(config('services.midtrans.server_key'));
    }

    /**
     * Buat ID pesanan unik untuk percobaan transaksi.
     */
    public function generateOrderId(Registration $registration): string
    {
        return 'VLS-REG-' . $registration->id . '-' . time() . '-' . Str::upper(Str::random(4));
    }

    /**
     * Buat transaksi Snap Midtrans.
     * Jika server key belum diset, otomatis mengembalikan mock token untuk pengujian lokal.
     */
    public function createSnapTransaction(Registration $registration, int $grossAmount): array
    {
        $orderId = $this->generateOrderId($registration);

        if (! $this->isConfigured()) {
            $mockToken = 'mock-snap-' . Str::random(24);

            $payment = Payment::create([
                'registration_id' => $registration->id,
                'order_id' => $orderId,
                'amount' => $grossAmount,
                'status' => Payment::STATUS_PENDING,
                'snap_token' => $mockToken,
                'payment_type' => 'simulation',
            ]);

            return [
                'token' => $mockToken,
                'redirect_url' => route('ticket.show', ['registration' => $registration->id]),
                'order_id' => $orderId,
                'payment_id' => $payment->id,
                'is_mock' => true,
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => mb_substr($registration->participant_name ?: ($registration->user?->name ?? 'Peserta'), 0, 50),
                'email' => $registration->user?->email ?? 'peserta@velosha.id',
                'phone' => $registration->user?->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => 'CAT-' . $registration->event_category_id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => mb_substr($registration->category?->name ?? 'Tiket Race', 0, 50),
                ],
            ],
            'callbacks' => [
                'finish' => route('ticket.show', ['registration' => $registration->id]),
            ],
        ];

        $serverKey = (string) config('services.midtrans.server_key');
        $snapUrl = (string) config('services.midtrans.snap_url');

        $response = Http::withBasicAuth($serverKey, '')
            ->timeout(15)
            ->post($snapUrl, $payload);

        if (! $response->successful()) {
            Log::error('Midtrans Snap Error: ' . $response->body());
            throw new \RuntimeException('Gagal menghubungkan ke payment gateway Midtrans.');
        }

        $data = $response->json();
        $token = $data['token'] ?? null;
        $redirectUrl = $data['redirect_url'] ?? null;

        $payment = Payment::create([
            'registration_id' => $registration->id,
            'order_id' => $orderId,
            'amount' => $grossAmount,
            'status' => Payment::STATUS_PENDING,
            'snap_token' => $token,
            'payload' => $data,
        ]);

        return [
            'token' => $token,
            'redirect_url' => $redirectUrl,
            'order_id' => $orderId,
            'payment_id' => $payment->id,
            'is_mock' => false,
        ];
    }

    /**
     * Verifikasi kode hash keamanan SHA-512 dari Midtrans.
     * Catatan: Ini adalah security hash checksum antar server, BUKAN tanda tangan manusia.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if (empty($serverKey)) {
            // Jika dalam mode mock lokal tanpa server key
            return true;
        }

        $calculated = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($calculated, $signatureKey);
    }

    /**
     * Cek status transaksi langsung ke server REST API Midtrans.
     */
    public function checkStatus(string $orderId): ?array
    {
        $serverKey = (string) config('services.midtrans.server_key');
        if (empty($serverKey)) {
            return null;
        }

        $isProduction = (bool) config('services.midtrans.is_production', false);
        $baseUrl = $isProduction ? 'https://api.midtrans.com/v2' : 'https://api.sandbox.midtrans.com/v2';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(3)
                ->get("{$baseUrl}/{$orderId}/status");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning("Midtrans checkStatus exception for {$orderId}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Cek apakah order Midtrans sudah lunas (settlement / capture).
     */
    public function isSettled(string $orderId): bool
    {
        $status = $this->checkStatus($orderId);
        if (! $status) {
            return false;
        }

        $trxStatus = $status['transaction_status'] ?? '';
        $fraudStatus = $status['fraud_status'] ?? '';

        return $trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept');
    }
}

