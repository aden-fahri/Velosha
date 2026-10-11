@use('App\Support\EventCardPresenter', 'P')
{{-- Langkah 3: periksa kembali data peserta sebelum pembayaran --}}
@if ($step === 3 && $registration !== null)
<div class="space-y-4">

    @if ($resumed)
    <div class="bg-race-50 border border-race-300 velo-box-1 p-4 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-start gap-3 flex-1 min-w-0">
            <i data-lucide="history" class="w-4 h-4 text-race-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="text-xs font-bold text-race-800">Melanjutkan pendaftaran yang belum selesai</p>
                <p class="text-[11px] text-race-700/80 mt-0.5">Data dari sesi sebelumnya sudah dimuat kembali. Periksa kembali atau mulai dari awal.</p>
            </div>
        </div>
        <button type="button" wire:click="resetToStart" class="shrink-0 inline-flex items-center justify-center bg-white hover:bg-velosurfacealt text-race-800 border border-race-300 font-bold px-3.5 py-2 text-[11px] velo-box-3 shadow-xs transition">
            <i data-lucide="rotate-ccw" class="w-3 h-3 mr-1.5"></i>
            <span>Mulai dari Awal</span>
        </button>
    </div>
    @endif

    <!-- Kartu Data Peserta -->
    <div class="bg-white border border-veloborder velo-box-1 p-6">
        <div class="flex items-center justify-between border-b border-velosurface pb-3 mb-4">
            <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider flex items-center">
                <span class="w-2 h-2 bg-race-500 velo-box-1 mr-2"></span>
                Data Peserta
            </h3>
            <button type="button" wire:click="backToStep2" class="inline-flex items-center text-[11px] font-bold text-race-600 hover:text-race-700 border border-race-300 bg-race-50 hover:bg-race-100 px-3 py-1.5 velo-box-3 transition">
                <i data-lucide="pencil" class="w-3 h-3 mr-1.5"></i>
                <span>Edit Data</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs">
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Nama Lengkap</span>
                <span class="font-bold text-teal-900 text-sm">{{ $registration->participant_name }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">NIK / Paspor</span>
                <span class="font-bold text-teal-900 text-sm">{{ $registration->identity_number }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Tanggal Lahir</span>
                <span class="font-bold text-teal-900">{{ $registration->birth_date?->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Jenis Kelamin</span>
                <span class="font-bold text-teal-900">{{ $registration->gender === 'female' ? 'Perempuan' : 'Laki-laki' }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Golongan Darah</span>
                <span class="font-bold text-red-700 bg-red-50 px-1.5 py-0.5 velo-box-3 border border-red-200">Golongan {{ $registration->blood_type }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Email E-Ticket</span>
                <span class="font-bold text-teal-900">{{ $registration->user?->email ?? $email }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Nomor WhatsApp</span>
                <span class="font-bold text-teal-900">{{ $phone }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Klub / Komunitas</span>
                <span class="font-medium text-teal-700">{{ $registration->club ?? '—' }}</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Kontak Darurat</span>
                <span class="font-medium text-teal-700">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_relation }})</span>
            </div>
            <div>
                <span class="text-velomuted block text-[10px] uppercase font-semibold">No HP Darurat</span>
                <span class="font-medium text-teal-700">{{ $registration->emergency_contact_phone }}</span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-velomuted block text-[10px] uppercase font-semibold">Catatan Medis</span>
                <span class="font-medium text-teal-700">{{ $registration->medical_notes !== '' && $registration->medical_notes !== null ? $registration->medical_notes : 'Tidak ada catatan medis' }}</span>
            </div>
        </div>
    </div>

    <!-- Strip Paket -->
    <div class="bg-white border border-veloborder velo-box-1 p-5">
        <h3 class="text-[10px] font-bold text-velomuted uppercase tracking-wider mb-3">Yang Anda Dapatkan</h3>
        <div class="grid grid-cols-3 gap-3">
            <div class="border border-velosurface velo-box-3 bg-velosurfacealt p-3 text-center">
                <i data-lucide="shirt" class="w-4 h-4 text-teal-700 mx-auto mb-1.5"></i>
                <div class="text-[11px] font-bold text-teal-900">Racepack</div>
                <div class="text-[10px] text-velomutedlight mt-0.5">Jersey &amp; BIB Tag</div>
            </div>
            <div class="border border-velosurface velo-box-3 bg-velosurfacealt p-3 text-center">
                <i data-lucide="shield-check" class="w-4 h-4 text-teal-700 mx-auto mb-1.5"></i>
                <div class="text-[11px] font-bold text-teal-900">Pendukung</div>
                <div class="text-[10px] text-velomutedlight mt-0.5">Premi Asuransi Penuh</div>
            </div>
            <div class="border border-velosurface velo-box-3 bg-velosurfacealt p-3 text-center">
                <i data-lucide="medal" class="w-4 h-4 text-teal-700 mx-auto mb-1.5"></i>
                <div class="text-[11px] font-bold text-teal-900">Finisir</div>
                <div class="text-[10px] text-velomutedlight mt-0.5">Medali &amp; Kaos Finish</div>
            </div>
        </div>
    </div>

</div>
@endif
