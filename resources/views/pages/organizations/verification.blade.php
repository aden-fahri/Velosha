@extends('layouts.app', ['title' => 'Verifikasi Penyelenggara — ' . $organization->name, 'nav' => false])

@section('content')
<x-layout.organizer-navbar :organization="$organization" />

<section class="py-8 px-4 sm:px-6 lg:px-8 bg-[var(--paper)] min-h-[calc(100vh-64px)]">
    <div class="max-w-4xl mx-auto space-y-8">

        {{-- Flash Messages --}}
        @if (session('status'))
            <div class="p-4 bg-[var(--pine-l)] border-2 border-[var(--ink)] text-[var(--ink)] rounded-xl shadow-[3px_3px_0_var(--ink)] flex items-center justify-between font-bold text-sm">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-xs font-black">&check;</span>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-[var(--red-l)] border-2 border-[var(--ink)] text-[var(--ink)] rounded-xl shadow-[3px_3px_0_var(--ink)] font-bold text-xs space-y-1">
                @foreach ($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <span class="text-[var(--red)] font-black">&times;</span>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Header Kartu Komunitas Penyelenggara --}}
        <div class="p-6 sm:p-8 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[6px_6px_0_var(--ink)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="chip font-black text-xs uppercase tracking-wider">
                    Penyelenggara
                </span>

                <h1 class="text-3xl sm:text-4xl font-black text-[var(--ink)] tracking-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                    {{ $organization->name }}
                </h1>
                <p class="text-xs sm:text-sm text-[var(--mute)] max-w-2xl font-medium">
                    Verifikasi identitas diperlukan agar komunitas Anda dapat menayangkan race dan menerima pendaftaran peserta resmi.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('organizations.events.create', $organization) }}"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-[var(--orange)] text-white font-extrabold text-sm rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ Buat Event Baru</span>
                </a>
            </div>
        </div>

        {{-- 4 Tab Navigasi Neobrutalist --}}
        <div class="flex flex-wrap gap-2.5 border-b-2 border-[var(--line)] pb-2">
            <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Event Saya</span>
            </a>

            <a href="{{ route('organizations.quota', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path><path d="M12 18V6"></path></svg>
                <span>Paket &amp; Kuota</span>
            </a>

            <a href="{{ route('organizations.finance', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                <span>Keuangan &amp; Payout</span>
            </a>

            <a href="{{ route('organizations.verification', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--mustard)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)] -translate-y-0.5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Verifikasi Identitas</span>
            </a>
        </div>

        {{-- Panel Verifikasi --}}
        <div class="p-8 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[6px_6px_0_var(--ink)] space-y-6">
            <h2 class="text-xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                Status Verifikasi Legalitas Komunitas
            </h2>

            @if ($organization->isVerified())
                <div class="p-6 rounded-2xl bg-[var(--pine-l)] border-2 border-[var(--ink)] space-y-3">
                    <div class="font-black text-base text-[var(--pine)] flex items-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Komunitas Anda Terverifikasi Resmi</span>
                    </div>
                    <p class="text-xs sm:text-sm text-[var(--ink)] font-medium leading-relaxed">
                        Organisasi Anda telah diverifikasi oleh tim Velosha pada {{ $organization->verified_at?->format('d M Y, H:i') ?? '—' }}. Anda memiliki izin penuh untuk mempublikasikan race sepeda dan mencairkan dana tiket.
                    </p>
                    @if ($organization->verification_document_path)
                        <div class="pt-2">
                            <a href="{{ route('organizations.document.download', $organization) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-black text-[var(--ink)] underline hover:text-[var(--orange)]">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Unduh Dokumen Verifikasi Terdaftar</span>
                            </a>
                        </div>
                    @endif
                </div>
            @elseif ($organization->isPendingVerification())
                <div class="p-6 rounded-2xl bg-[var(--butter)] border-2 border-[var(--ink)] space-y-3">
                    <div class="font-black text-base text-[var(--ink)] flex items-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>Dokumen Sedang Ditinjau Admin</span>
                    </div>
                    <p class="text-xs sm:text-sm text-[var(--ink)] font-medium leading-relaxed">
                        Dokumen verifikasi Anda telah kami terima dan saat ini sedang dalam antrean verifikasi tim admin Velosha (maksimal 1x24 jam kerja).
                    </p>
                    @if ($organization->verification_document_path)
                        <div class="pt-2">
                            <a href="{{ route('organizations.document.download', $organization) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-black text-[var(--ink)] underline hover:text-[var(--orange)]">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Lihat Berkas yang Diunggah</span>
                            </a>
                        </div>
                    @endif
                </div>
            @else
                @if ($organization->isRejectedVerification())
                    <div class="p-5 rounded-xl bg-[var(--red-l)] border-2 border-[var(--ink)] space-y-1">
                        <div class="font-black text-sm text-[var(--red)]">Verifikasi Sebelumnya Ditolak</div>
                        <p class="text-xs text-[var(--ink)] font-medium">
                            Catatan admin: {{ $organization->rejection_note ?? 'Dokumen tidak jelas atau tidak sesuai.' }}
                        </p>
                    </div>
                @endif

                <form action="{{ route('organizations.verification.submit', $organization) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="document" class="block text-xs font-black uppercase tracking-wider text-[var(--ink)] mb-2">
                            Unggah Berkas KTP / Akta Komunitas / NPWP <span class="text-[var(--red)]">*</span>
                        </label>
                        <input type="file" id="document" name="document" required accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] text-xs font-bold text-[var(--ink)] file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border file:border-[var(--ink)] file:bg-[var(--card)] file:font-black file:text-xs">
                        <p class="text-[11px] font-semibold text-[var(--mute)] mt-1.5">
                            Format berkas: PDF, JPG, atau PNG (Maksimal 5 MB).
                        </p>
                    </div>

                    <button type="submit"
                            class="px-6 py-3 bg-[var(--orange)] text-white font-extrabold text-sm rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                        Kirim Dokumen Verifikasi →
                    </button>
                </form>
            @endif
        </div>

    </div>
</section>
@endsection
