<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrganizationController extends Controller
{
    public function create(): View
    {
        return view('pages.organizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
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

        $organization->users()->attach($request->user()->id, [
            'role' => 'owner',
        ]);

        return redirect()->route('organizations.verification', ['organization' => $organization->slug])
            ->with('status', 'Komunitas / Organisasi berhasil didaftarkan! Silakan lengkapi verifikasi identitas agar dapat mempublikasikan race.');
    }

    /**
     * Tampilkan status verifikasi dan formulir unggah dokumen identitas organisasi.
     */
    public function verification(Organization $organization): View
    {
        $this->authorizeMember($organization);

        return view('pages.organizations.verification', [
            'organization' => $organization,
        ]);
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

