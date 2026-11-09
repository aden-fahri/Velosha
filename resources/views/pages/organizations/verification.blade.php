@extends('layouts.app', ['title' => 'Verifikasi Penyelenggara — ' . $organization->name])

@section('content')
<div class="bg-velosurfacealt min-h-[calc(100vh-140px)] py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Notifikasi Status & Error --}}
        @if (session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-semibold velo-box-1 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 text-xs font-semibold velo-box-1 space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Header Organisasi --}}
        <div class="bg-white border border-veloborderstrong p-6 sm:p-8 velo-box-1 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-velomuted">SaaS &bull; Verifikasi Penyelenggara</span>
                <h1 class="text-2xl font-black text-teal-900 mt-1 flex items-center gap-2">
                    <span>{{ $organization->name }}</span>
                    @if ($organization->isVerified())
                    <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <i data-lucide="badge-check" class="w-3.5 h-3.5 mr-1 text-emerald-600"></i>
                        VERIFIED
                    </span>
                    @endif
                </h1>
                <p class="text-xs text-velomuted mt-1">
                    Verifikasi identitas diperlukan agar komunitas/organisasi Anda dapat menayangkan race dan menerima pendaftaran peserta resmi.
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('organizations.quota', ['organization' => $organization->slug]) }}"
                   class="inline-flex items-center text-xs font-bold px-3 py-2 border border-veloborder hover:bg-velosurfacealt text-teal-900 velo-box-3 transition">
                    <i data-lucide="coins" class="w-3.5 h-3.5 mr-1.5 text-race-600"></i>
                    <span>Kuota Event</span>
                </a>
            </div>
        </div>

        {{-- Panel Status Verifikasi --}}
        <div class="bg-white border border-veloborderstrong p-6 velo-box-1 space-y-6">
            <h2 class="text-sm font-bold text-teal-900 uppercase tracking-wider border-b border-veloborder pb-3">
                Status Verifikasi Komunitas
            </h2>

            @if ($organization->isVerified())
                {{-- Sudah Terverifikasi --}}
                <div class="p-5 bg-emerald-50 border border-emerald-200 velo-box-1 flex items-start space-x-3">
                    <i data-lucide="shield-check" class="w-6 h-6 text-emerald-600 shrink-0 mt-0.5"></i>
                    <div class="space-y-1">
                        <div class="text-sm font-bold text-emerald-900">Komunitas Terverifikasi Resmi</div>
                        <p class="text-xs text-emerald-700">
                            Organisasi Anda telah diverifikasi oleh tim Velosha pada {{ $organization->verified_at?->format('d M Y, H:i') ?? '—' }}.
                            Anda memiliki izin penuh untuk mempublikasikan race sepeda di platform.
                        </p>
                        @if ($organization->verification_document_path)
                        <div class="pt-2">
                            <a href="{{ route('organizations.document.download', ['organization' => $organization->slug]) }}"
                               class="inline-flex items-center text-xs font-semibold text-emerald-800 hover:underline">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 mr-1"></i>
                                <span>Unduh Dokumen Verifikasi Terdaftar</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

            @elseif ($organization->isPendingVerification())
                {{-- Menunggu Review --}}
                <div class="p-5 bg-amber-50 border border-amber-200 velo-box-1 flex items-start space-x-3">
                    <i data-lucide="clock" class="w-6 h-6 text-amber-600 shrink-0 mt-0.5"></i>
                    <div class="space-y-1">
                        <div class="text-sm font-bold text-amber-900">Menunggu Peninjauan Admin</div>
                        <p class="text-xs text-amber-800">
                            Dokumen verifikasi Anda telah kami terima dan saat ini sedang dalam antrean verifikasi tim admin Velosha. Proses verifikasi biasanya memakan waktu maksimal 1x24 jam kerja.
                        </p>
                        @if ($organization->verification_document_path)
                        <div class="pt-2">
                            <a href="{{ route('organizations.document.download', ['organization' => $organization->slug]) }}"
                               class="inline-flex items-center text-xs font-semibold text-amber-900 hover:underline">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 mr-1"></i>
                                <span>Lihat Berkas yang Diunggah</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

            @elseif ($organization->isRejectedVerification())
                {{-- Ditolak dengan Alasan --}}
                <div class="p-5 bg-red-50 border border-red-200 velo-box-1 space-y-3">
                    <div class="flex items-start space-x-3">
                        <i data-lucide="alert-octagon" class="w-6 h-6 text-red-600 shrink-0 mt-0.5"></i>
                        <div>
                            <div class="text-sm font-bold text-red-900">Pengajuan Verifikasi Belum Disetujui</div>
                            <p class="text-xs text-red-700 mt-0.5">
                                Mohon maaf, dokumen verifikasi belum memenuhi syarat kelayakan platform.
                            </p>
                        </div>
                    </div>
                    @if ($organization->rejection_note)
                    <div class="bg-white/80 border border-red-200 p-3 velo-box-3 text-xs text-red-900">
                        <span class="font-bold">Catatan Tim Peninjau:</span>
                        <p class="mt-0.5">{{ $organization->rejection_note }}</p>
                    </div>
                    @endif
                </div>

            @else
                {{-- Belum Mengajukan --}}
                <div class="p-5 bg-velosurfacealt border border-veloborder velo-box-1 flex items-start space-x-3">
                    <i data-lucide="info" class="w-6 h-6 text-velomuted shrink-0 mt-0.5"></i>
                    <div class="space-y-1">
                        <div class="text-sm font-bold text-teal-900">Belum Mengajukan Verifikasi</div>
                        <p class="text-xs text-velomuted">
                            Unggah berkas identitas penanggung jawab komunitas (KTP / Paspor / Surat Keterangan Komunitas) di bawah ini untuk mengaktifkan fitur publikasi event.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Formulir Unggah Dokumen (Hanya jika belum verified atau ingin revisi) --}}
            @if (! $organization->isVerified())
            <div class="pt-4 border-t border-veloborder">
                <h3 class="text-xs font-extrabold text-teal-900 uppercase tracking-wider mb-2">
                    {{ $organization->isRejectedVerification() ? 'Unggah Ulang Dokumen Revisi' : 'Unggah Dokumen Verifikasi' }}
                </h3>
                <p class="text-xs text-velomuted mb-4">
                    Format file yang didukung: <strong>PDF, JPG, JPEG, PNG</strong> (Maksimal 5 MB). Dokumen disimpan secara terenkripsi dan privat di server Velosha.
                </p>

                <form action="{{ route('organizations.verification.submit', ['organization' => $organization->slug]) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="space-y-4">
                    @csrf
                    <div>
                        <label for="document" class="block text-xs font-bold text-teal-900 mb-1">
                            Pilih Berkas Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="file"
                               name="document"
                               id="document"
                               required
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-xs text-velomuted file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-xs file:font-bold file:bg-[var(--orange)] file:text-[var(--ink)] hover:file:bg-[var(--orange-l)] file:cursor-pointer border border-veloborderstrong p-2 velo-box-1 bg-white">
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-bold px-6 py-3 text-xs velo-box-1 shadow-sm transition">
                        <i data-lucide="upload-cloud" class="w-4 h-4 mr-2"></i>
                        <span>Kirim Dokumen Verifikasi</span>
                    </button>
                </form>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

