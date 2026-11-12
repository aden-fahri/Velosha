@use('App\Support\EventCardPresenter', 'P')
{{-- Panel sticky ringkasan biaya, pemilihan metode Midtrans, & pemicu pembayaran --}}
@php($methods = [
    'qris' => ['label' => 'QRIS', 'hint' => 'GoPay, OVO, ShopeePay'],
    'bca_va' => ['label' => 'BCA VA', 'hint' => 'Virtual Account'],
    'mandiri' => ['label' => 'Mandiri', 'hint' => 'Bill Payment'],
    'bri_va' => ['label' => 'BRI VA', 'hint' => 'Virtual Account'],
    'bni_va' => ['label' => 'BNI VA', 'hint' => 'Virtual Account'],
    'card' => ['label' => 'Kartu Kredit', 'hint' => 'Segera'],
])
<div class="lg:col-span-4 sticky top-24 space-y-4">
    <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl p-6 shadow-[4px_4px_0_var(--ink)]">
        <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider border-b-2 border-[var(--ink)] pb-3">
            Ringkasan Pembayaran
        </h3>

        <div class="my-4 space-y-3 text-xs">
            @if ($category !== null)
            <div class="flex justify-between text-[var(--mute)]">
                <span>Tiket {{ $category->name }} ({{ P::distance($category->distance_km) }})</span>
                <span class="font-bold text-[var(--ink)]">Rp {{ P::angka((float) $category->price) }}</span>
            </div>
            @else
            <div class="text-[var(--mute)] text-center border-2 border-dashed border-[var(--ink)]/30 rounded-xl p-3 font-medium">
                Pilih kategori jarak pada langkah 1 untuk melihat rincian biaya.
            </div>
            @endif
            <div class="flex justify-between text-[var(--mute)]">
                <span>Sewa RFID Timing Chip</span>
                <span class="font-bold text-[var(--pine)]">Termasuk (Gratis)</span>
            </div>
            <div class="flex justify-between text-[var(--mute)]">
                <span>Premi Asuransi Jiwa &amp; Kecelakaan</span>
                <span class="font-bold text-[var(--pine)]">Termasuk (Gratis)</span>
            </div>
            <div class="flex justify-between text-[var(--mute)]">
                <span>Biaya Layanan</span>
                <span class="font-bold text-[var(--ink)]">Rp {{ P::angka((float) \App\Livewire\Checkout::SERVICE_FEE) }}</span>
            </div>

            <div class="pt-3 border-t-2 border-[var(--ink)] flex justify-between items-baseline">
                <span class="text-xs font-black text-[var(--ink)] uppercase tracking-wider">Total Pembayaran</span>
                <span class="text-2xl font-black text-[var(--orange)]" style="font-family: 'Bricolage Grotesque', sans-serif">Rp {{ P::angka((float) $this->total()) }}</span>
            </div>
        </div>

        @if ($step === 3)
        {{-- Grid metode pembayaran (interaktif) --}}
        <div class="border-t-2 border-[var(--ink)] pt-4">
            <div class="text-[10px] uppercase font-black tracking-wider text-[var(--mute)] mb-2.5">Metode Pembayaran Midtrans</div>
            <div class="grid grid-cols-2 gap-2.5">
                @foreach ($methods as $key => $method)
                @if ($key === 'card')
                <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 py-2.5 px-2 text-center opacity-60 cursor-not-allowed" title="Segera hadir">
                    <div class="text-[11px] font-bold text-gray-500">{{ $method['label'] }}</div>
                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $method['hint'] }}</div>
                </div>
                @else
                <button type="button" wire:click="selectPaymentMethod('{{ $key }}')"
                    class="border-2 border-[var(--ink)] rounded-xl py-2.5 px-2 text-center transition cursor-pointer {{ $paymentMethod === $key ? 'bg-[#F4BE3F] shadow-[2.5px_2.5px_0_var(--ink)] font-black' : 'bg-white hover:bg-[#FAF9F5] text-[var(--ink)] font-bold' }}">
                    <div class="text-[11px] font-black leading-tight">{{ $method['label'] }}</div>
                    <div class="text-[10px] text-[var(--mute)] mt-0.5 leading-tight">{{ $method['hint'] }}</div>
                </button>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Countdown batas waktu checkout --}}
        <div class="mt-4 p-3 bg-[var(--paper)] border-2 border-[var(--ink)] rounded-xl text-center shadow-[2px_2px_0_var(--ink)]"
             x-data="{ s: 900 }"
             x-init="setInterval(() => { if (s > 0) s--; }, 1000)">
            <div class="text-[10px] uppercase font-black text-[var(--mute)] tracking-wider mb-0.5">Batas Waktu Checkout</div>
            <div class="text-lg font-black font-mono text-[var(--orange)] tracking-wider" x-text="String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0')">15:00</div>
        </div>

        {{-- Error feedback langsung di atas tombol --}}
        @if ($error !== '')
        <div class="mt-3 p-3.5 rounded-xl border-2 border-red-500 bg-red-100 text-red-900 text-xs font-bold shadow-[2px_2px_0_#991b1b] flex items-start gap-2">
            <svg class="w-4 h-4 shrink-0 text-red-700 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>{{ $error }}</div>
        </div>
        @endif

        <button type="button" wire:click="openPayment" wire:loading.attr="disabled"
                class="mt-4 w-full flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] disabled:opacity-60 text-[var(--ink)] font-black py-3.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
            <span wire:loading.remove wire:target="openPayment" class="inline-flex items-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Lanjutkan Pembayaran Midtrans</span>
            </span>
            <span wire:loading wire:target="openPayment" class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-[var(--ink)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span>Menghubungkan ke Midtrans...</span>
            </span>
        </button>
        <p class="text-[10px] text-velomuted text-center mt-2">Pembayaran diproses melalui Midtrans Snap.</p>
        @elseif ($step === 2)
        <p class="text-center text-[10px] text-velomutedlight border border-dashed border-veloborderstrong velo-box-3 p-3 mt-4">
            Selesaikan data diri pada langkah 2 untuk membuka pembayaran.
        </p>
        @else
        <p class="text-center text-[10px] text-velomutedlight border border-dashed border-veloborderstrong velo-box-3 p-3 mt-4">
            Langkah 1 dari 3 — pilih kategori jarak untuk melanjutkan.
        </p>
        @endif

        <p class="text-[10px] text-velomuted text-center mt-3">
            Enkripsi TLS 1.3 &middot; Tiket &amp; BIB diterbitkan seketika setelah pembayaran berhasil
        </p>
    </div>
</div>
