@use('App\Support\EventCardPresenter', 'P')
<div class="bg-velosurfacealt py-10 min-h-[calc(100vh-140px)]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Clean Page Header -->
        <div class="bg-[#2E343B] text-white p-6 mb-8 velo-box-1 border border-white/10 shadow-sm">
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Pendaftaran &amp; Konfirmasi Tiket
            </h1>
            <p class="text-xs text-[#d1d5db] mt-1">Lengkapi data diri resmi peserta maraton untuk alokasi nomor BIB dan asuransi medis.</p>
        </div>

        @if ($error !== '')
        <div class="mb-6 border border-red-300 bg-red-50 text-red-700 text-xs p-3.5 velo-box-1 flex items-start space-x-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
            <span>{{ $error }}</span>
        </div>
        @endif

        <!-- 3-Step Progress Indicator -->
        <div class="mb-8 bg-white border border-veloborder velo-box-1 p-4">
            @php
                $stepLabels = [1 => 'Kategori Race', 2 => 'Data Diri & Medis', 3 => 'Pembayaran Midtrans'];
            @endphp
            <div class="grid grid-cols-3 gap-2 text-xs">
                @foreach ($stepLabels as $number => $label)
                <div class="flex items-center space-x-3 pb-2 border-b-2 {{ $step >= $number ? 'text-teal-900 font-bold border-race-500' : 'text-[#5f6a70] border-veloborder' }}">
                    <span class="w-6 h-6 flex items-center justify-center velo-box-3 text-xs {{ $step >= $number ? 'bg-race-600 text-white' : 'bg-velosurface text-[#556066]' }}">{{ $number }}</span>
                    <span class="truncate">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Form Input (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Langkah 1: Pilih Kategori Race -->
                @if ($step === 1 && $event !== null)
                <div class="bg-white border border-veloborder velo-box-1 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider flex items-center">
                            <span class="w-2 h-2 bg-race-500 velo-box-1 mr-2"></span>
                            Pilih Kategori Jarak
                        </h3>
                        <a href="{{ route('events.show', $event->slug) }}" wire:navigate class="text-xs text-race-500 hover:underline">Ganti Event</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($event->categories as $cat)
                        <button type="button" wire:click="selectCategory({{ $cat->id }})"
                            class="w-full text-left p-4 velo-box-3 flex items-center justify-between {{ $categoryId === $cat->id ? 'border-2 border-race-500 bg-race-50/40' : 'border border-veloborderstrong bg-white hover:border-[#93a0a1] cursor-pointer' }}">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 flex items-center justify-center font-bold text-xs velo-box-3 {{ $categoryId === $cat->id ? 'bg-race-600 text-white' : 'bg-velosurfacealt text-teal-700' }}">{{ $cat->code }}</div>
                                <div>
                                    <div class="text-sm font-bold text-teal-900">{{ $cat->name }} ({{ P::distance($cat->distance_km) }})</div>
                                    <div class="text-xs text-[#556066]">Elevasi +{{ P::angka((float) $cat->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $cat->cut_off_hours) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-extrabold text-teal-900">Rp {{ P::angka((float) $cat->price) }}</div>
                                <span class="text-[10px] px-1.5 py-0.5 velo-box-3 font-semibold {{ $cat->remaining > 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-red-700 bg-red-50 border border-red-200' }}">
                                    {{ $cat->remaining > 0 ? 'Sisa slot '.$cat->remaining : 'Slot Penuh' }}
                                </span>
                            </div>
                        </button>
                        @empty
                        <div class="p-4 border border-veloborder bg-velosurfacealt velo-box-3 text-xs text-[#556066] text-center">
                            Kategori jarak belum tersedia untuk event ini.
                        </div>
                        @endforelse
                    </div>

                    @if ($event->categories->isNotEmpty())
                    <button type="button" wire:click="toStep2" class="mt-5 w-full flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold py-3.5 text-xs velo-box-1 shadow-sm transition">
                        <span>Lanjutkan ke Data Diri</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-2"></i>
                    </button>
                    @endif
                </div>
                @endif

                <!-- Section 1: Kategori yang dipilih (Langkah 2 & 3) -->
                @if ($step >= 2 && $category !== null)
                <div class="bg-white border border-veloborder velo-box-1 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider flex items-center">
                            <span class="w-2 h-2 bg-race-500 velo-box-1 mr-2"></span>
                            Kategori Jarak Dipilih
                        </h3>
                        <button type="button" wire:click="toStep1" class="text-xs text-race-500 hover:underline">Ubah</button>
                    </div>

                    <div class="border-2 border-race-500 bg-race-50/40 p-4 velo-box-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-race-600 text-white flex items-center justify-center font-bold text-xs velo-box-3">
                                {{ $category->code }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-teal-900">{{ $category->name }} ({{ P::distance($category->distance_km) }})</h4>
                                <div class="text-xs text-[#556066]">Elevasi +{{ P::angka((float) $category->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $category->cut_off_hours) }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-extrabold text-teal-900">Rp {{ P::angka((float) $category->price) }}</div>
                            <span class="text-[10px] px-1.5 py-0.5 velo-box-3 font-semibold {{ $category->remaining > 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-red-700 bg-red-50 border border-red-200' }}">
                                {{ $category->remaining > 0 ? 'Tersedia' : 'Slot Penuh' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Langkah 2: Formulir Data Diri & Kontak Darurat -->
                @if ($step === 2)
                <!-- Section 2: Data Diri Peserta -->
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

                <!-- Section 3: Kontak Darurat (Safety First) -->
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

                <!-- Navigasi Langkah 2 -->
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

                <!-- Langkah 3: Periksa Kembali Data -->
                @if ($step === 3 && $registration !== null)
                <div class="bg-white border border-veloborder velo-box-1 p-6 space-y-4">
                    <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider flex items-center border-b border-velosurface pb-3">
                        <span class="w-2 h-2 bg-race-500 velo-box-1 mr-2"></span>
                        Periksa Kembali Data Peserta
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs">
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Nama Lengkap</span>
                            <span class="font-bold text-teal-900 text-sm">{{ $registration->participant_name }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">NIK / Paspor</span>
                            <span class="font-bold text-teal-900 text-sm">{{ $registration->identity_number }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Tanggal Lahir</span>
                            <span class="font-bold text-teal-900">{{ $registration->birth_date?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Jenis Kelamin</span>
                            <span class="font-bold text-teal-900">{{ $registration->gender === 'female' ? 'Perempuan' : 'Laki-laki' }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Golongan Darah</span>
                            <span class="font-bold text-red-700 bg-red-50 px-1.5 py-0.5 velo-box-3 border border-red-200">Golongan {{ $registration->blood_type }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Email E-Ticket</span>
                            <span class="font-bold text-teal-900">{{ $registration->user?->email ?? $email }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Nomor WhatsApp</span>
                            <span class="font-bold text-teal-900">{{ $phone }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Klub / Komunitas</span>
                            <span class="font-medium text-teal-700">{{ $registration->club ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Kontak Darurat</span>
                            <span class="font-medium text-teal-700">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_relation }})</span>
                        </div>
                        <div>
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">No HP Darurat</span>
                            <span class="font-medium text-teal-700">{{ $registration->emergency_contact_phone }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Catatan Medis</span>
                            <span class="font-medium text-teal-700">{{ $registration->medical_notes !== '' && $registration->medical_notes !== null ? $registration->medical_notes : 'Tidak ada catatan medis' }}</span>
                        </div>
                    </div>

                    <div class="border-t border-veloborder pt-4">
                        <button type="button" wire:click="backToStep2" class="text-xs text-race-500 hover:underline">
                            Ubah data diri &amp; kontak darurat
                        </button>
                    </div>
                </div>
                @endif

            </div>

            <!-- Right: Order Summary & Midtrans Payment Trigger (4 Cols) -->
            <div class="lg:col-span-4 sticky top-24 space-y-4">
                <div class="bg-white border border-veloborderstrong p-6 velo-box-1 shadow-sm">
                    <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider border-b border-veloborder pb-3">
                        Ringkasan Pembayaran
                    </h3>

                    <div class="my-4 space-y-3 text-xs">
                        @if ($category !== null)
                        <div class="flex justify-between text-[#4b555b]">
                            <span>Tiket {{ $category->name }} ({{ P::distance($category->distance_km) }})</span>
                            <span class="font-medium text-teal-900">Rp {{ P::angka((float) $category->price) }}</span>
                        </div>
                        @else
                        <div class="text-[#556066] text-center border border-dashed border-veloborderstrong velo-box-3 p-3">
                            Pilih kategori jarak pada langkah 1 untuk melihat rincian biaya.
                        </div>
                        @endif
                        <div class="flex justify-between text-[#4b555b]">
                            <span>Sewa RFID Timing Chip</span>
                            <span class="font-medium text-emerald-700">Termasuk (Gratis)</span>
                        </div>
                        <div class="flex justify-between text-[#4b555b]">
                            <span>Premi Asuransi Jiwa &amp; Kecelakaan</span>
                            <span class="font-medium text-emerald-700">Termasuk (Gratis)</span>
                        </div>
                        <div class="flex justify-between text-[#4b555b]">
                            <span>Biaya Layanan Midtrans</span>
                            <span class="font-medium text-teal-900">Rp {{ P::angka((float) \App\Livewire\Checkout::SERVICE_FEE) }}</span>
                        </div>

                        <div class="pt-3 border-t border-veloborder flex justify-between items-baseline">
                            <span class="text-xs font-bold text-teal-900">Total Pembayaran</span>
                            <span class="text-xl font-extrabold text-teal-900">Rp {{ P::angka((float) $this->total()) }}</span>
                        </div>
                    </div>

                    <!-- Payment Methods Supported -->
                    <div class="p-3 bg-velosurfacealt border border-veloborder velo-box-3 my-4">
                        <div class="text-[10px] uppercase font-bold text-[#556066] mb-2">Metode Didukung Midtrans:</div>
                        <div class="grid grid-cols-3 gap-1.5 text-[10px] text-center font-medium text-teal-700">
                            <span class="bg-white border border-veloborder py-1 velo-box-3">QRIS</span>
                            <span class="bg-white border border-veloborder py-1 velo-box-3">BCA VA</span>
                            <span class="bg-white border border-veloborder py-1 velo-box-3">Mandiri</span>
                            <span class="bg-white border border-veloborder py-1 velo-box-3">BRI VA</span>
                            <span class="bg-white border border-veloborder py-1 velo-box-3">BNI VA</span>
                            <span class="bg-white border border-veloborder py-1 velo-box-3">Visa/Master</span>
                        </div>
                    </div>

                    <!-- Pay Now Button (Directly opens Midtrans Simulation Modal) -->
                    @if ($step === 3)
                    <button type="button" wire:click="openPayment" class="w-full flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold py-3.5 text-xs velo-box-1 shadow-sm transition">
                        <i data-lucide="lock" class="w-3.5 h-3.5 mr-2"></i>
                        <span>Bayar Sekarang (Midtrans Snap)</span>
                    </button>
                    @elseif ($step === 2)
                    <p class="text-center text-[10px] text-[#556066] border border-dashed border-veloborderstrong velo-box-3 p-3">
                        Selesaikan data diri pada langkah 2 untuk membuka pembayaran.
                    </p>
                    @else
                    <p class="text-center text-[10px] text-[#556066] border border-dashed border-veloborderstrong velo-box-3 p-3">
                        Langkah 1 dari 3 — pilih kategori jarak untuk melanjutkan.
                    </p>
                    @endif

                    <p class="text-[10px] text-[#5f6a70] text-center mt-3">
                        Enkripsi TLS 1.3 &middot; Tiket &amp; BIB diterbitkan seketika setelah pembayaran berhasil
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Midtrans Snap Simulation Modal -->
@if ($showPaymentModal && $registration !== null)
<div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" wire:click="closePayment">
    <div class="bg-white max-w-md w-full velo-box-1 border border-veloborderstrong shadow-2xl overflow-hidden velo-modal-enter" @click.stop>
        <!-- Snap Header -->
        <div class="bg-race-600 text-white px-5 py-3.5 flex justify-between items-center border-b border-race-500">
            <div class="flex items-center space-x-2">
                <span class="font-extrabold text-sm tracking-tight text-white">MIDTRANS SNAP</span>
                <span class="text-[10px] bg-white/20 px-1.5 py-0.5 velo-box-3 font-mono">SIMULATION</span>
            </div>
            <button type="button" wire:click="closePayment" class="text-white/60 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Snap Body -->
        <div class="p-6 space-y-4" @click.stop>
            <div class="text-center pb-4 border-b border-veloborder">
                <div class="text-xs text-[#556066]">Order ID: VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="text-2xl font-extrabold text-teal-900 mt-1">Rp {{ P::angka((float) $this->total()) }}</div>
                <div class="text-xs text-[#4b555b] mt-0.5">Velosha &mdash; {{ $event?->title ?? 'Velosha Race' }}</div>
            </div>

            <div class="space-y-2">
                <div class="text-xs font-bold text-teal-700">Pilih Metode Pembayaran:</div>

                <label @class(['flex items-center justify-between p-3 border velo-box-3 cursor-pointer transition', 'border-race-500 bg-race-50/50' => $paymentMethod === 'qris', 'border-veloborder hover:border-[#93a0a1] bg-white' => $paymentMethod !== 'qris'])>
                    <div class="flex items-center space-x-2.5">
                        <input type="radio" name="pay_opt" wire:click="selectPaymentMethod('qris')" {{ $paymentMethod === 'qris' ? 'checked' : '' }} class="text-teal-900">
                        <span class="text-xs font-bold text-teal-800">QRIS (GoPay, OVO, ShopeePay, Dana)</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 velo-box-3">Instan</span>
                </label>

                <label @class(['flex items-center justify-between p-3 border velo-box-3 cursor-pointer transition', 'border-race-500 bg-race-50/50' => $paymentMethod === 'bca_va', 'border-veloborder hover:border-[#93a0a1] bg-white' => $paymentMethod !== 'bca_va'])>
                    <div class="flex items-center space-x-2.5">
                        <input type="radio" name="pay_opt" wire:click="selectPaymentMethod('bca_va')" {{ $paymentMethod === 'bca_va' ? 'checked' : '' }} class="text-teal-900">
                        <span class="text-xs font-semibold text-teal-700">BCA Virtual Account</span>
                    </div>
                    <span class="text-[10px] text-[#5f6a70]">Verifikasi Otomatis</span>
                </label>

                <label @class(['flex items-center justify-between p-3 border velo-box-3 cursor-pointer transition', 'border-race-500 bg-race-50/50' => $paymentMethod === 'mandiri', 'border-veloborder hover:border-[#93a0a1] bg-white' => $paymentMethod !== 'mandiri'])>
                    <div class="flex items-center space-x-2.5">
                        <input type="radio" name="pay_opt" wire:click="selectPaymentMethod('mandiri')" {{ $paymentMethod === 'mandiri' ? 'checked' : '' }} class="text-teal-900">
                        <span class="text-xs font-semibold text-teal-700">Mandiri Bill Payment</span>
                    </div>
                    <span class="text-[10px] text-[#5f6a70]">Verifikasi Otomatis</span>
                </label>
            </div>

            <!-- Detail pembayaran sesuai metode terpilih (simulasi) -->
            @if ($paymentMethod === 'bca_va')
            <div class="p-4 bg-velosurfacealt border border-veloborder velo-box-3 text-center">
                <div class="text-[11px] font-semibold text-teal-700 mb-2">Transfer ke Virtual Account BCA ini:</div>
                @php($vaNumber = '8808'.str_pad((string) $registration->id, 12, '0', STR_PAD_LEFT))
                <div class="bg-white border border-veloborderstrong velo-box-1 py-3 font-mono text-base font-bold tracking-wider text-teal-900">{{ wordwrap($vaNumber, 4, ' ', true) }}</div>
                <button type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $vaNumber }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        class="mt-3 w-full border border-veloborderstrong bg-white hover:bg-velosurface text-teal-800 text-xs font-bold py-2 velo-box-3 transition">
                    <span x-text="copied ? 'Nomor tersalin!' : 'Salin Nomor VA'">Salin Nomor VA</span>
                </button>
                <div class="text-[10px] text-[#5f6a70] mt-2">Nomor VA dibuat khusus untuk order ini. Bayar penuh agar verifikasi otomatis.</div>
            </div>
            @elseif ($paymentMethod === 'mandiri')
            <div class="p-4 bg-velosurfacealt border border-veloborder velo-box-3 text-center">
                <div class="text-[11px] font-semibold text-teal-700 mb-2">Bayar Mandiri Bill Payment dengan Bill Key:</div>
                @php($billKey = '9080'.str_pad((string) $registration->id, 12, '0', STR_PAD_LEFT))
                <div class="bg-white border border-veloborderstrong velo-box-1 py-3 font-mono text-base font-bold tracking-wider text-teal-900">{{ wordwrap($billKey, 3, ' ', true) }}</div>
                <button type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $billKey }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        class="mt-3 w-full border border-veloborderstrong bg-white hover:bg-velosurface text-teal-800 text-xs font-bold py-2 velo-box-3 transition">
                    <span x-text="copied ? 'Bill key tersalin!' : 'Salin Bill Key'">Salin Bill Key</span>
                </button>
                <div class="text-[10px] text-[#5f6a70] mt-2">Buka Livin' by Mandiri &rarr; Bayar &rarr; Multipayment &rarr; masukkan company code 70012.</div>
            </div>
            @else
            <div class="p-4 bg-velosurfacealt border border-veloborder velo-box-3 text-center">
                <div class="text-[11px] font-semibold text-teal-700 mb-2">Pindai QRIS untuk menyelesaikan simulasi:</div>
                <div class="inline-block p-2 bg-white border border-veloborderstrong velo-box-1">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ rawurlencode('MIDTRANS-VELOSHA-REG-'.str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT).'-PAID') }}" alt="QR Code Midtrans" class="w-32 h-32 mx-auto">
                </div>
            </div>
            @endif

            <!-- Hitung mundur batas bayar -->
            <div class="text-center text-[10px] text-[#5f6a70] font-mono" x-data="{ s: 900 }" x-init="setInterval(() => { if (s > 0) s--; }, 1000)">
                Batas Waktu Bayar: <span class="font-bold text-race-600" x-text="String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0')">15:00</span> menit
            </div>

            <!-- Final Confirm Button -->
            <button type="button" wire:click="simulatePaid" wire:loading.attr="disabled" class="w-full flex items-center justify-center bg-emerald-800 hover:bg-emerald-900 text-white font-bold py-3 text-xs velo-box-1 shadow-sm transition disabled:opacity-60">
                <span wire:loading.remove wire:target="simulatePaid" class="flex items-center">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Simulasikan Pembayaran Berhasil
                </span>
                <span wire:loading wire:target="simulatePaid">Memproses pembayaran...</span>
            </button>

            <p class="text-[10px] text-[#5f6a70] text-center">
                Mode simulasi: tidak ada transaksi nyata yang diproses. Integrasi Midtrans asli menyusul.
            </p>
        </div>
    </div>
</div>
@endif
