<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\PayoutAccount;
use App\Models\PayoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrganizationFinanceController extends Controller
{
    /**
     * Tampilkan dasbor keuangan, rekening pencairan, buku kas ledger, dan pengajuan penarikan dana.
     */
    public function index(Organization $organization): RedirectResponse
    {
        $this->authorizeMember($organization);

        return redirect()->route('organizations.show', ['organization' => $organization->slug, 'tab' => 'keuangan']);
    }

    /**
     * Daftarkan rekening bank pencairan baru untuk organisasi.
     */
    public function storeAccount(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorizeMember($organization);

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:150'],
        ]);

        $hasPrimary = $organization->payoutAccounts()->where('is_primary', true)->exists();

        $organization->payoutAccounts()->create([
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'is_primary' => ! $hasPrimary,
        ]);

        return redirect()->route('organizations.finance', ['organization' => $organization->slug])
            ->with('status', 'Rekening bank pencairan berhasil ditambahkan.');
    }

    /**
     * Ajukan penarikan dana hasil penjualan tiket race.
     */
    public function requestPayout(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorizeMember($organization);

        if (! $organization->isVerified()) {
            return redirect()->route('organizations.finance', ['organization' => $organization->slug])
                ->with('error', 'Komunitas / organisasi harus berstatus terverifikasi sebelum dapat mengajukan pencairan dana.');
        }

        $validated = $request->validate([
            'payout_account_id' => ['required', 'exists:payout_accounts,id'],
            'amount' => ['required', 'integer', 'min:10000'],
        ]);

        $account = $organization->payoutAccounts()->findOrFail($validated['payout_account_id']);
        $amount = (int) $validated['amount'];
        $available = $organization->availablePayoutBalance();

        if ($amount > $available) {
            return redirect()->route('organizations.finance', ['organization' => $organization->slug])
                ->with('error', 'Nominal penarikan (Rp ' . number_format($amount, 0, ',', '.') . ') melebihi saldo yang tersedia (Rp ' . number_format($available, 0, ',', '.') . ').');
        }

        PayoutRequest::create([
            'organization_id' => $organization->id,
            'payout_account_id' => $account->id,
            'amount' => $amount,
            'status' => PayoutRequest::STATUS_PENDING,
            'requested_by' => Auth::id(),
        ]);

        return redirect()->route('organizations.finance', ['organization' => $organization->slug])
            ->with('status', 'Pengajuan pencairan dana sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dikirimkan. Tim finance Velosha akan memproses transfer.');
    }

    /**
     * Pastikan pengguna saat ini adalah anggota organisasi atau admin platform.
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
