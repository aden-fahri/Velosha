@if ($step === 2)
{{-- Langkah 2: data diri peserta (sesuai KTP) --}}
<div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-6 space-y-5">
    <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider flex items-center gap-2 border-b-2 border-[var(--ink)] pb-3">
        <span class="w-2.5 h-2.5 rounded-sm bg-[var(--orange)] border border-[var(--ink)]"></span>
        <span>Data Pribadi Peserta (Sesuai KTP)</span>
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nama Lengkap Peserta *</label>
            <input type="text" wire:model="participant_name" placeholder="Sesuai KTP" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
            @error('participant_name') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nomor Induk Kependudukan (NIK / Paspor) *</label>
            <input type="text" wire:model="identity_number" placeholder="16 digit NIK" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
            @error('identity_number') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Tanggal Lahir *</label>
            <input type="date" wire:model="birth_date" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
            @error('birth_date') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Jenis Kelamin *</label>
            <select wire:model="gender" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-bold">
                <option value="male">Laki-laki</option>
                <option value="female">Perempuan</option>
            </select>
            @error('gender') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Golongan Darah *</label>
            <select wire:model="blood_type" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-bold">
                <option value="O">O</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
            </select>
            @error('blood_type') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Email Konfirmasi E-Ticket *</label>
            <input type="email" wire:model="email" placeholder="nama@email.com" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
            @error('email') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nomor WhatsApp Aktif *</label>
            <input type="tel" wire:model="phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
            @error('phone') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nama Klub Sepeda / Komunitas (Opsional)</label>
        <input type="text" wire:model="club" placeholder="Contoh: Menoreh Peloton CC" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
        @error('club') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Kontak darurat wajib: syarat penerbitan polis asuransi & tim medis --}}
<div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-6 space-y-4">
    <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider flex items-center border-b-2 border-[var(--ink)] pb-3">
        <span class="w-2.5 h-2.5 bg-red-600 border border-[var(--ink)] rounded-xs mr-2"></span>
        <span>Kontak Darurat (Wajib untuk Asuransi &amp; Tim Medis)</span>
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nama Kontak Darurat *</label>
            <input type="text" wire:model="emergency_contact_name" placeholder="Nama lengkap" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
            @error('emergency_contact_name') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Hubungan *</label>
            <select wire:model="emergency_contact_relation" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-bold">
                <option>Istri / Suami</option>
                <option>Orang Tua</option>
                <option>Saudara Kandung</option>
                <option>Teman Satu Tim</option>
            </select>
            @error('emergency_contact_relation') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--ink)] mb-1">No HP Kontak Darurat *</label>
            <input type="tel" wire:model="emergency_contact_phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
            @error('emergency_contact_phone') <p class="text-[11px] font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-[var(--ink)] mb-1">Catatan Alergi / Riwayat Medis (Jika Ada)</label>
        <input type="text" wire:model="medical_notes" placeholder="Tidak ada alergi obat maupun makanan" class="w-full px-3 py-2.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] bg-white font-medium">
        @error('medical_notes') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Navigasi langkah 2 --}}
<div class="flex items-center justify-between gap-3 pt-2">
    <button type="button" wire:click="toStep1" class="px-5 py-3 bg-white hover:bg-[var(--paper)] text-[var(--ink)] border-2 border-[var(--ink)] rounded-xl text-xs font-black shadow-[2px_2px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5 inline mr-1.5"></i>
        <span>Kembali</span>
    </button>
    <button type="button" wire:click="toStep3" class="flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black px-6 py-3 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
        <span>Lanjut ke Langkah Pembayaran</span>
        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
    </button>
</div>
@endif
