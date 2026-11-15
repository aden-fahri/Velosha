@use('App\Support\EventCardPresenter', 'P')
{{-- Langkah 3: periksa kembali data peserta sebelum pembayaran --}}
@if ($step === 3 && $registration !== null)
<div class="space-y-4">

    @if ($resumed)
    <div class="bg-[#F8E3B5] border-2 border-[#10282E] rounded-xl p-4 shadow-[3px_3px_0_#10282E] flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-start gap-3 flex-1 min-w-0">
            <i data-lucide="history" class="w-4 h-4 text-[#10282E] shrink-0 mt-0.5"></i>
            <div>
                <p class="text-xs font-bold text-[#10282E]">Melanjutkan pendaftaran yang belum selesai</p>
                <p class="text-[11px] text-[#10282E]/80 mt-0.5">Data dari sesi sebelumnya sudah dimuat kembali. Periksa kembali atau mulai dari awal.</p>
            </div>
        </div>
        <button type="button" wire:click="resetToStart" wire:loading.attr="disabled" class="shrink-0 inline-flex items-center justify-center bg-white hover:bg-[#FFFDF8] text-[#10282E] border-2 border-[#10282E] font-black px-3.5 py-2 text-[11px] rounded-lg shadow-[2px_2px_0_#10282E] transition active:translate-y-0.5 cursor-pointer">
            <i data-lucide="rotate-ccw" class="w-3 h-3 mr-1.5"></i>
            <span>Mulai dari Awal</span>
        </button>
    </div>
    @endif

    <!-- Kartu Data Peserta -->
    <div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-6">
        <div class="flex items-center justify-between border-b-2 border-[var(--ink)] pb-3 mb-4">
            <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider">
                Data Peserta
            </h3>
            <button type="button" wire:click="backToStep2" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 text-xs font-bold text-[var(--orange)] hover:underline border-2 border-[var(--ink)] bg-[var(--paper)] hover:bg-[var(--butter)] px-3 py-1.5 rounded-xl shadow-[2px_2px_0_var(--ink)] transition-all cursor-pointer">
                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                <span>Edit Data</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs">
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Nama Lengkap</span>
                <span class="font-bold text-[var(--ink)] text-sm">{{ $registration->participant_name }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">NIK / Paspor</span>
                <span class="font-bold text-[var(--ink)] text-sm">{{ $registration->identity_number }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Tanggal Lahir</span>
                <span class="font-bold text-[var(--ink)]">{{ $registration->birth_date?->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Jenis Kelamin</span>
                <span class="font-bold text-[var(--ink)]">{{ $registration->gender === 'female' ? 'Perempuan' : 'Laki-laki' }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Golongan Darah</span>
                <span class="font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-md border border-red-300">Golongan {{ $registration->blood_type }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Email E-Ticket</span>
                <span class="font-bold text-[var(--ink)]">{{ $registration->user?->email ?? $email }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Nomor WhatsApp</span>
                <span class="font-bold text-[var(--ink)]">{{ $phone }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Klub / Komunitas</span>
                <span class="font-medium text-[var(--ink)]">{{ $registration->club ?? '—' }}</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Kontak Darurat</span>
                <span class="font-medium text-[var(--ink)]">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_relation }})</span>
            </div>
            <div>
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">No HP Darurat</span>
                <span class="font-medium text-[var(--ink)]">{{ $registration->emergency_contact_phone }}</span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-[var(--mute)] block text-[10px] uppercase font-semibold">Catatan Medis</span>
                <span class="font-medium text-[var(--ink)]">{{ $registration->medical_notes !== '' && $registration->medical_notes !== null ? $registration->medical_notes : 'Tidak ada catatan medis' }}</span>
            </div>
        </div>
    </div>

    <!-- Strip Paket -->
    <div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-5">
        <h3 class="text-xs font-black text-[var(--ink)] uppercase tracking-wider mb-3">Yang Anda Dapatkan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            <div class="border-2 border-[var(--ink)] rounded-xl bg-[var(--paper)] p-3 sm:p-3.5 text-center shadow-[2px_2px_0_var(--ink)] flex items-center sm:flex-col justify-start sm:justify-center gap-3 sm:gap-0">
                <i data-lucide="shirt" class="w-5 h-5 text-[var(--pine)] sm:mx-auto sm:mb-1.5 shrink-0"></i>
                <div class="text-left sm:text-center">
                    <div class="text-xs font-black text-[var(--ink)]">Racepack</div>
                    <div class="text-[10px] text-[var(--mute)] font-medium sm:mt-0.5">Jersey &amp; BIB Tag</div>
                </div>
            </div>
            <div class="border-2 border-[var(--ink)] rounded-xl bg-[var(--paper)] p-3 sm:p-3.5 text-center shadow-[2px_2px_0_var(--ink)] flex items-center sm:flex-col justify-start sm:justify-center gap-3 sm:gap-0">
                <i data-lucide="shield-check" class="w-5 h-5 text-[var(--pine)] sm:mx-auto sm:mb-1.5 shrink-0"></i>
                <div class="text-left sm:text-center">
                    <div class="text-xs font-black text-[var(--ink)]">Pendukung</div>
                    <div class="text-[10px] text-[var(--mute)] font-medium sm:mt-0.5">Premi Asuransi Penuh</div>
                </div>
            </div>
            <div class="border-2 border-[var(--ink)] rounded-xl bg-[var(--paper)] p-3 sm:p-3.5 text-center shadow-[2px_2px_0_var(--ink)] flex items-center sm:flex-col justify-start sm:justify-center gap-3 sm:gap-0">
                <i data-lucide="medal" class="w-5 h-5 text-[var(--pine)] sm:mx-auto sm:mb-1.5 shrink-0"></i>
                <div class="text-left sm:text-center">
                    <div class="text-xs font-black text-[var(--ink)]">Finisir</div>
                    <div class="text-[10px] text-[var(--mute)] font-medium sm:mt-0.5">Medali &amp; Kaos Finish</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endif
