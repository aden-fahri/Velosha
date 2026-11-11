<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPhoto;
use App\Models\Organization;
use App\Models\QuotaPackage;
use App\Models\QuotaPurchase;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrganizationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && ! $request->has('new')) {
            $existing = $user->organizations()->first() ?? $user->ownedOrganizations()->first();
            if ($existing) {
                return redirect()->route('organizations.show', $existing);
            }
        }

        return view('pages.organizations.create', [
            'packageId' => $request->query('package'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'package_id' => ['nullable', 'exists:quota_packages,id'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug !== '' ? $baseSlug : 'komunitas';
        $originalSlug = $slug;
        $counter = 1;

        while (Organization::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $organization = Organization::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'verification_status' => Organization::VERIFICATION_UNVERIFIED,
        ]);

        $organization->users()->attach(Auth::id(), [
            'role' => 'owner',
        ]);

        if (! empty($validated['package_id'])) {
            return redirect()->route('organizations.quota', ['organization' => $organization->slug, 'selected' => $validated['package_id']])
                ->with('status', 'Komunitas berhasil didaftarkan! Silakan selesaikan pembelian paket kuota untuk mulai membuat event.');
        }

        return redirect()->route('organizations.verification', ['organization' => $organization->slug])
            ->with('status', 'Komunitas / Organisasi berhasil didaftarkan! Silakan lengkapi verifikasi identitas agar dapat mempublikasikan race.');
    }

    /**
     * Dasbor terpadu penyelenggara (Event Saya, Kuota, Keuangan, & Verifikasi).
     */
    public function show(Request $request, Organization $organization, MidtransService $midtrans): View
    {
        $this->authorizeMember($organization);

        // Sinkronisasi otomatis pembayaran paket kuota Midtrans (hanya saat kembali dari callback dengan parameter order_id)
        if ($request->filled('order_id')) {
            $orderId = (string) $request->query('order_id');
            $purchase = $organization->quotaPurchases()
                ->where('order_id', $orderId)
                ->where('status', QuotaPurchase::STATUS_PENDING)
                ->first();

            if ($purchase) {
                $isPaid = false;
                $payType = 'midtrans';
                $payPayload = [];

                if ($midtrans->isConfigured()) {
                    $status = $midtrans->checkStatus($purchase->order_id);
                    if ($status) {
                        $trxStatus = $status['transaction_status'] ?? '';
                        $fraudStatus = $status['fraud_status'] ?? '';
                        if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                            $isPaid = true;
                            $payType = $status['payment_type'] ?? 'midtrans';
                            $payPayload = $status;
                        }
                    }
                }

                // Fallback: Jika URL callback membawa parameter settlement untuk order ini
                if (! $isPaid) {
                    $qStatus = $request->query('transaction_status');
                    if (in_array($qStatus, ['settlement', 'capture', 'success'], true)) {
                        $isPaid = true;
                        $payType = 'midtrans-finish';
                        $payPayload = ['query' => $request->query()];
                    }
                }

                if ($isPaid) {
                    $purchase->markAsPaid($payType, $payPayload);
                    session()->flash('status', 'Pembayaran paket kuota berhasil diverifikasi! Kuota Anda telah bertambah.');
                }
            }
        }

        // Pastikan setiap event yang dibuat organisasi telah memotong kuota di ledger
        foreach ($organization->events()->get() as $ev) {
            $hasDeduction = $organization->quotaLedger()->where('event_id', $ev->id)->exists();
            if (! $hasDeduction && $organization->quotaBalance() > 0) {
                $organization->deductQuotaForEvent($ev);
            }
        }

        $balance = $organization->quotaBalance();

        $tab = $request->query('tab', 'overview');
        $events = $organization->events()->with('categories')->latest('event_date')->get();
        $packages = QuotaPackage::active()->orderBy('price')->get();
        $ledger = $organization->quotaLedger()->with(['purchase.package', 'event'])->latest('id')->take(10)->get();

        // Data finansial
        $financialBalance = $organization->financialBalance();
        $availableBalance = $organization->availablePayoutBalance();
        $pendingPayout = $organization->pendingPayoutAmount();
        $totalSales = $organization->totalGrossSales();
        $totalPaidOut = $organization->totalPaidOut();
        $bankAccounts = $organization->payoutAccounts()->latest()->get();
        $payoutRequests = $organization->payoutRequests()->with('account')->latest('id')->take(10)->get();
        $financialLedger = $organization->financialLedger()->with('event')->latest('id')->take(20)->get();

        return view('pages.organizations.show', [
            'organization' => $organization,
            'activeTab' => $tab,
            'balance' => $balance,
            'events' => $events,
            'packages' => $packages,
            'ledger' => $ledger,
            'financialBalance' => $financialBalance,
            'availableBalance' => $availableBalance,
            'pendingPayout' => $pendingPayout,
            'totalSales' => $totalSales,
            'totalPaidOut' => $totalPaidOut,
            'bankAccounts' => $bankAccounts,
            'payoutRequests' => $payoutRequests,
            'financialLedger' => $financialLedger,
        ]);
    }

    /**
     * Tampilkan formulir pembuatan event baru untuk penyelenggara.
     */
    public function createEvent(Organization $organization): View
    {
        $this->authorizeMember($organization);

        $packages = QuotaPackage::active()->orderBy('price')->get();
        $balance = $organization->quotaBalance();

        return view('pages.organizations.events.create', [
            'organization' => $organization,
            'packages' => $packages,
            'balance' => $balance,
            'activeTab' => 'create_event',
        ]);
    }

    /**
     * Simpan event baru beserta kategori tiket awal.
     */
    public function storeEvent(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorizeMember($organization);

        $balance = $organization->quotaBalance();
        if ($balance <= 0) {
            return redirect()->route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota'])
                ->with('error', 'Kuota event Anda kosong (0). Silakan pilih paket kuota terlebih dahulu sebelum membuat event.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'type' => ['required', 'in:gran_fondo,medio_fondo,fun_ride,climb'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
            'location_name' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'poster' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'categories' => ['nullable', 'array', 'min:1'],
            'categories.*.name' => ['required_with:categories', 'string', 'max:100'],
            'categories.*.price' => ['required_with:categories', 'numeric', 'min:0'],
            'categories.*.quota' => ['required_with:categories', 'integer', 'min:1'],
            'categories.*.distance_km' => ['nullable', 'numeric', 'min:0'],
            'categories.*.cut_off_hours' => ['nullable', 'numeric', 'min:0'],
            'category_name' => ['nullable', 'string', 'max:100'],
            'category_price' => ['nullable', 'numeric', 'min:0'],
            'category_quota' => ['nullable', 'integer', 'min:1'],
            'category_distance' => ['nullable', 'numeric', 'min:0'],
            'category_cut_off' => ['nullable', 'numeric', 'min:0'],
        ]);

        $slug = Event::generateUniqueSlug($validated['title']);
        $status = Event::STATUS_PUBLISHED;

        $event = Event::create([
            'user_id' => Auth::id(),
            'organization_id' => $organization->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'location_name' => $validated['location_name'],
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'],
            'type' => $validated['type'],
            'status' => $status,
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('events', 'public');
            $event->photos()->create([
                'photo_path' => $path,
                'sort_order' => 1,
            ]);
        }

        // Simpan kategori-kategori tiket balap
        $categoriesInput = $request->input('categories');
        if (is_array($categoriesInput) && count($categoriesInput) > 0) {
            foreach ($categoriesInput as $catData) {
                if (empty($catData['name'])) {
                    continue;
                }
                $catName = trim($catData['name']);
                $catCode = Str::upper(substr(preg_replace('/[^A-Za-z0-9]/', '', $catName), 0, 6)) ?: 'CAT';

                $event->categories()->create([
                    'name' => $catName,
                    'code' => $catCode,
                    'distance_km' => ! empty($catData['distance_km']) ? (float) $catData['distance_km'] : 100.00,
                    'elevation_gain_m' => 450.00,
                    'price' => (float) ($catData['price'] ?? 0),
                    'quota' => (int) ($catData['quota'] ?? 100),
                    'wave_start_time' => $validated['start_time'],
                    'cut_off_hours' => ! empty($catData['cut_off_hours']) ? (float) $catData['cut_off_hours'] : 6.0,
                ]);
            }
        } elseif (! empty($validated['category_name'])) {
            $catName = $validated['category_name'];
            $catCode = Str::upper(substr(preg_replace('/[^A-Za-z0-9]/', '', $catName), 0, 6)) ?: 'CAT1';

            $event->categories()->create([
                'name' => $catName,
                'code' => $catCode,
                'distance_km' => $validated['category_distance'] ?? 100.00,
                'elevation_gain_m' => 450.00,
                'price' => $validated['category_price'] ?? 0,
                'quota' => $validated['category_quota'] ?? 100,
                'wave_start_time' => $validated['start_time'],
                'cut_off_hours' => $validated['category_cut_off'] ?? 6.0,
            ]);
        }

        // Selalu potong kuota 1 saat membuat event
        $organization->deductQuotaForEvent($event);

        $sisa = $organization->quotaBalance();

        $msg = "Event '{$event->title}' berhasil dipublikasikan! Kuota terpotong 1 (Sisa kuota: {$sisa} Race).";
        if ($sisa <= 0) {
            $msg .= " Kuota Anda telah habis. Untuk menerbitkan race baru berikutnya, silakan beli paket kuota.";
        }

        return redirect()->route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events'])
            ->with('status', $msg);
    }

    /**
     * Tampilkan formulir edit event untuk penyelenggara.
     */
    public function editEvent(Organization $organization, Event $event): View
    {
        $this->authorizeMember($organization);
        abort_if((int) $event->organization_id !== (int) $organization->id, 404);

        $event->load(['categories', 'photos']);
        $balance = $organization->quotaBalance();

        return view('pages.organizations.events.edit', [
            'organization' => $organization,
            'event' => $event,
            'categories' => $event->categories,
            'category' => $event->categories->first(),
            'balance' => $balance,
            'activeTab' => 'events',
        ]);
    }

    /**
     * Simpan perubahan data event dari penyelenggara.
     */
    public function updateEvent(Request $request, Organization $organization, Event $event): RedirectResponse
    {
        $this->authorizeMember($organization);
        abort_if((int) $event->organization_id !== (int) $organization->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'type' => ['required', 'in:gran_fondo,medio_fondo,fun_ride,climb'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required'],
            'location_name' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'poster' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'categories' => ['nullable', 'array', 'min:1'],
            'categories.*.name' => ['required_with:categories', 'string', 'max:100'],
            'categories.*.price' => ['required_with:categories', 'numeric', 'min:0'],
            'categories.*.quota' => ['required_with:categories', 'integer', 'min:1'],
            'categories.*.distance_km' => ['nullable', 'numeric', 'min:0'],
            'categories.*.cut_off_hours' => ['nullable', 'numeric', 'min:0'],
            'category_name' => ['nullable', 'string', 'max:100'],
            'category_price' => ['nullable', 'numeric', 'min:0'],
            'category_quota' => ['nullable', 'integer', 'min:1'],
            'category_distance' => ['nullable', 'numeric', 'min:0'],
            'category_cut_off' => ['nullable', 'numeric', 'min:0'],
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location_name' => $validated['location_name'],
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'],
            'type' => $validated['type'],
        ]);

        $categoriesInput = $request->input('categories');
        if (is_array($categoriesInput) && count($categoriesInput) > 0) {
            $submittedIds = [];
            foreach ($categoriesInput as $catData) {
                if (empty($catData['name'])) {
                    continue;
                }
                $catName = trim($catData['name']);
                $catCode = Str::upper(substr(preg_replace('/[^A-Za-z0-9]/', '', $catName), 0, 6)) ?: 'CAT';

                $catId = ! empty($catData['id']) ? (int) $catData['id'] : null;
                $cat = $catId ? $event->categories()->where('id', $catId)->first() : null;

                if ($cat) {
                    $cat->update([
                        'name' => $catName,
                        'distance_km' => ! empty($catData['distance_km']) ? (float) $catData['distance_km'] : $cat->distance_km,
                        'price' => (float) ($catData['price'] ?? $cat->price),
                        'quota' => (int) ($catData['quota'] ?? $cat->quota),
                        'cut_off_hours' => ! empty($catData['cut_off_hours']) ? (float) $catData['cut_off_hours'] : $cat->cut_off_hours,
                    ]);
                    $submittedIds[] = $cat->id;
                } else {
                    $newCat = $event->categories()->create([
                        'name' => $catName,
                        'code' => $catCode,
                        'distance_km' => ! empty($catData['distance_km']) ? (float) $catData['distance_km'] : 100.00,
                        'elevation_gain_m' => 450.00,
                        'price' => (float) ($catData['price'] ?? 0),
                        'quota' => (int) ($catData['quota'] ?? 100),
                        'wave_start_time' => $validated['start_time'],
                        'cut_off_hours' => ! empty($catData['cut_off_hours']) ? (float) $catData['cut_off_hours'] : 6.0,
                    ]);
                    $submittedIds[] = $newCat->id;
                }
            }

            // Hapus kategori yang dihapus pengguna di form HANYA jika belum memiliki pendaftar (registrations = 0)
            if (! empty($submittedIds)) {
                $event->categories()->whereNotIn('id', $submittedIds)->doesntHave('registrations')->delete();
            }
        } elseif (! empty($validated['category_name'])) {
            $cat = $event->categories()->first();
            if ($cat) {
                $cat->update([
                    'name' => $validated['category_name'],
                    'distance_km' => $validated['category_distance'] ?? $cat->distance_km,
                    'price' => $validated['category_price'],
                    'quota' => $validated['category_quota'],
                    'cut_off_hours' => $validated['category_cut_off'] ?? $cat->cut_off_hours,
                ]);
            }
        }

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('events', 'public');
            /** @var EventPhoto|null $firstPhoto */
            $firstPhoto = $event->photos()->orderBy('sort_order')->first();
            if ($firstPhoto) {
                $firstPhoto->update(['photo_path' => $path]);
            } else {
                $event->photos()->create([
                    'photo_path' => $path,
                    'sort_order' => 1,
                ]);
            }
        }

        return redirect()->route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events'])
            ->with('status', "Perubahan event '{$event->title}' berhasil disimpan.");
    }

    /**
     * Tampilkan status verifikasi dan formulir unggah dokumen identitas organisasi.
     */
    public function verification(Organization $organization): RedirectResponse
    {
        $this->authorizeMember($organization);

        return redirect()->route('organizations.show', ['organization' => $organization->slug, 'tab' => 'overview']);
    }

    /**
     * Unggah dokumen identitas / legalitas penanggung jawab organisasi.
     */
    public function submitVerification(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorizeMember($organization);

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('document')->store('verification_documents', 'local');

        $organization->submitVerification($path);

        return redirect()->route('organizations.verification', ['organization' => $organization->slug])
            ->with('status', 'Dokumen verifikasi berhasil dikirimkan. Tim admin Velosha akan meninjau dalam 1x24 jam.');
    }

    /**
     * Unduh berkas dokumen verifikasi privat (hanya admin & anggota organisasi).
     */
    public function downloadDocument(Organization $organization): StreamedResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $isMember = $organization->users()->where('users.id', $user->id)->exists();
        if (! $user->isAdmin() && ! $isMember) {
            abort(403, 'Akses ditolak.');
        }

        if (blank($organization->verification_document_path) || ! Storage::disk('local')->exists($organization->verification_document_path)) {
            abort(404, 'Dokumen verifikasi tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $organization->verification_document_path,
            'Verifikasi-' . $organization->slug . '.' . pathinfo($organization->verification_document_path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Pastikan pengguna saat ini adalah anggota organisasi atau admin platform.
     */
    protected function authorizeMember(Organization $organization): void
    {
        /** @var User|null $user */
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

