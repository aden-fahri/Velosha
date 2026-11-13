<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\QuotaPackage;
use App\Models\QuotaPurchase;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationQuotaController extends Controller
{
    /**
     * Halaman publik katalog paket kuota & harga untuk penyelenggara.
     */
    public function publicPackages(): View
    {
        $packages = QuotaPackage::active()->orderBy('price')->get();

        return view('pages.packages', [
            'packages' => $packages,
        ]);
    }

    /**
     * Tampilkan saldo kuota, daftar paket, dan riwayat mutasi organisasi.
     */
    public function index(Organization $organization): RedirectResponse
    {
        $this->authorizeMember($organization);

        return redirect()->route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']);
    }

    /**
     * Beli paket kuota event via Midtrans Snap.
     */
    public function purchase(Request $request, Organization $organization, MidtransService $midtrans): RedirectResponse
    {
        $this->authorizeMember($organization);

        $validated = $request->validate([
            'package_id' => ['required', 'exists:quota_packages,id'],
        ]);

        $package = QuotaPackage::active()->findOrFail($validated['package_id']);
        $user = Auth::user();
        $orderId = 'VLS-QUOTA-' . $organization->id . '-' . time() . '-' . Str::upper(Str::random(4));

        $purchase = QuotaPurchase::create([
            'organization_id' => $organization->id,
            'quota_package_id' => $package->id,
            'user_id' => $user->id,
            'order_id' => $orderId,
            'amount' => $package->price,
            'status' => QuotaPurchase::STATUS_PENDING,
        ]);

        if (! $midtrans->isConfigured()) {
            // Mode simulasi lokal jika Midtrans API Key di .env masih kosong
            $purchase->markAsPaid('simulation', ['note' => 'Local development mock settlement']);

            $redirectRoute = $request->input('redirect_to') === 'events.create'
                ? route('organizations.events.create', ['organization' => $organization->slug])
                : route('organizations.quota', ['organization' => $organization->slug]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_mock' => true,
                    'redirect_url' => $redirectRoute,
                ]);
            }

            return redirect($redirectRoute)
                ->with('status', "Pembelian paket '{$package->name}' berhasil! Kuota bertambah +{$package->events_count} event. Anda sekarang resmi menjadi Penyelenggara.");
        }

        // Mode Midtrans Asli
        $serverKey = (string) config('services.midtrans.server_key');
        $snapUrl = (string) config('services.midtrans.snap_url');

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $package->price,
            ],
            'customer_details' => [
                'first_name' => mb_substr($user->name ?: $organization->name, 0, 50),
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => 'PKG-' . $package->id,
                    'price' => $package->price,
                    'quantity' => 1,
                    'name' => mb_substr($package->name . ' (' . $package->events_count . ' Kuota Event)', 0, 50),
                ],
            ],
            'callbacks' => [
                'finish' => route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']),
            ],
        ];

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(15)
                ->post($snapUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $purchase->update([
                    'snap_token' => $data['token'] ?? null,
                    'payload' => $data,
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'snap_token' => $data['token'] ?? null,
                        'order_id' => $orderId,
                        'redirect_url' => $data['redirect_url'] ?? null,
                        'finish_url' => route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']),
                    ]);
                }

                if (! empty($data['redirect_url'])) {
                    return redirect()->away($data['redirect_url']);
                }
            } else {
                Log::error('Midtrans Quota Purchase Error: ' . $response->body());

                return redirect()->route('organizations.quota', ['organization' => $organization->slug])
                    ->with('error', 'Gagal memproses pembayaran Midtrans. Silakan coba beberapa saat lagi.');
            }
        } catch (\Throwable $e) {
            Log::error('Midtrans Quota Purchase Exception: ' . $e->getMessage());

            return redirect()->route('organizations.quota', ['organization' => $organization->slug])
                ->with('error', 'Terjadi kendala jaringan saat menghubungi gateway Midtrans.');
        }

        return redirect()->route('organizations.quota', ['organization' => $organization->slug])
            ->with('status', "Pesanan paket telah dibuat (Order ID: {$orderId}). Silakan selesaikan pembayaran.");
    }

    /**
     * Pastikan user adalah admin atau anggota dari organisasi ini.
     */
    protected function authorizeMember(Organization $organization): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(401);
        }

        if ($user->isAdmin()) {
            return;
        }

        $isMember = $organization->users()->where('users.id', $user->id)->exists();

        if (! $isMember) {
            abort(403, 'Anda bukan anggota dari komunitas/organisasi ini.');
        }
    }
}

