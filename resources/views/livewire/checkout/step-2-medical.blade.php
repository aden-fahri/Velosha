@if ($step === 2)
{{-- Langkah 2: data diri peserta (sesuai KTP) --}}
<div class="bg-white border border-veloborder velo-box-1 p-6 space-y-4">
    <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider flex items-center border-b border-velosurface pb-3">
        <span class="w-2 h-2 bg-race-500 velo-box-1 mr-2"></span>
        Data Pribadi Peserta (Sesuai KTP)
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Nama Lengkap Peserta *</label>
            <input type="text" wire:model="participant_name" placeholder="Sesuai KTP" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
            @error('participant_name') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Nomor Induk Kependudukan (NIK / Paspor) *</label>
            <input type="text" wire:model="identity_number" placeholder="16 digit NIK" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
            @error('identity_number') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Tanggal Lahir *</label>
            <input type="date" wire:model="birth_date" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
            @error('birth_date') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Jenis Kelamin *</label>
            <select wire:model="gender" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
                <option value="male">Laki-laki</option>
                <option value="female">Perempuan</option>
            </select>
            @error('gender') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Golongan Darah *</label>
            <select wire:model="blood_type" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
                <option value="O">O</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
            </select>
            @error('blood_type') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Email Konfirmasi E-Ticket *</label>
            <input type="email" wire:model="email" placeholder="nama@email.com" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
            @error('email') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Nomor WhatsApp Aktif *</label>
            <input type="tel" wire:model="phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
            @error('phone') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-teal-700 mb-1">Nama Klub Sepeda / Komunitas (Opsional)</label>
        <input type="text" wire:model="club" placeholder="Contoh: Menoreh Peloton CC" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
        @error('club') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Kontak darurat wajib: syarat penerbitan polis asuransi & tim medis --}}
<div class="bg-white border border-veloborder velo-box-1 p-6 space-y-4">
    <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider flex items-center border-b border-velosurface pb-3">
        <span class="w-2 h-2 bg-red-600 velo-box-1 mr-2"></span>
        Kontak Darurat (Wajib untuk Asuransi &amp; Tim Medis)
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Nama Kontak Darurat *</label>
            <input type="text" wire:model="emergency_contact_name" placeholder="Nama lengkap" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
            @error('emergency_contact_name') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">Hubungan *</label>
            <select wire:model="emergency_contact_relation" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
                <option>Istri / Suami</option>
                <option>Orang Tua</option>
                <option>Saudara Kandung</option>
                <option>Teman Satu Tim</option>
            </select>
            @error('emergency_contact_relation') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-teal-700 mb-1">No HP Kontak Darurat *</label>
            <input type="tel" wire:model="emergency_contact_phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
            @error('emergency_contact_phone') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-teal-700 mb-1">Catatan Alergi / Riwayat Medis (Jika Ada)</label>
        <input type="text" wire:model="medical_notes" placeholder="Tidak ada alergi obat maupun makanan" class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
        @error('medical_notes') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Navigasi langkah 2 --}}
<div class="flex items-center justify-between gap-3">
    <button type="button" wire:click="toStep1" class="px-4 py-3 bg-white hover:bg-velosurfacealt text-teal-800 border border-veloborderstrong velo-box-1 text-xs font-semibold shadow-xs transition">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5 mr-1.5"></i>
        Kembali
    </button>
    <button type="button" wire:click="toStep3" class="flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-6 py-3 text-xs velo-box-1 shadow-sm transition">
        <span>Lanjut ke Langkah Pembayaran</span>
        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-2"></i>
    </button>
</div>
@endif
