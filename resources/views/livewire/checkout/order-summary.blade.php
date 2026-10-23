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
    <div class="bg-white border border-veloborderstrong p-6 velo-box-1 shadow-sm">
        <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider border-b border-veloborder pb-3">
            Ringkasan Pembayaran
        </h3>

        <div class="my-4 space-y-3 text-xs">
            @if ($category !== null)
            <div class="flex justify-between text-velomutedlight">
                <span>Tiket {{ $category->name }} ({{ P::distance($category->distance_km) }})</span>
                <span class="font-medium text-teal-900">Rp {{ P::angka((float) $category->price) }}</span>
            </div>
            @else
            <div class="text-velomutedlight text-center border border-dashed border-veloborderstrong velo-box-3 p-3">
                Pilih kategori jarak pada langkah 1 untuk melihat rincian biaya.
            </div>
            @endif
            <div class="flex justify-between text-velomutedlight">
                <span>Sewa RFID Timing Chip</span>
                <span class="font-medium text-emerald-700">Termasuk (Gratis)</span>
            </div>
            <div class="flex justify-between text-velomutedlight">
                <span>Premi Asuransi Jiwa &amp; Kecelakaan</span>
                <span class="font-medium text-emerald-700">Termasuk (Gratis)</span>
            </div>
            <div class="flex justify-between text-velomutedlight">
                <span>Biaya Layanan</span>
                <span class="font-medium text-teal-900">Rp {{ P::angka((float) \App\Livewire\Checkout::SERVICE_FEE) }}</span>
            </div>

            <div class="pt-3 border-t border-veloborder flex justify-between items-baseline">
                <span class="text-xs font-bold text-teal-900">Total Pembayaran</span>
                <span class="text-xl font-extrabold text-race-600">Rp {{ P::angka((float) $this->total()) }}</span>
            </div>
        </div>

        @if ($step === 3)
        {{-- Grid metode pembayaran (interaktif) --}}
        <div class="border-t border-veloborder pt-4">
            <div class="text-[10px] uppercase font-bold text-velomutedlight mb-2.5">Metode Pembayaran Midtrans</div>
            <div class="grid grid-cols-3 gap-1.5">
                @foreach ($methods as $key => $method)
                @if ($key === 'card')
                <div class="border border-veloborder bg-velosurfacealt velo-box-3 p-2 text-center opacity-60 cursor-not-allowed" title="Segera hadir">
                    <div class="text-[10px] font-bold text-velomuted">{{ $method['label'] }}</div>
                    <div class="text-[9px] text-velomutedlight mt-0.5">{{ $method['hint'] }}</div>
                </div>
                @else
                <button type="button" wire:click="selectPaymentMethod('{{ $key }}')"
                    class="border velo-box-3 p-2 text-center transition {{ $paymentMethod === $key ? 'border-race-500 bg-race-50/60 shadow-xs' : 'border-veloborder bg-white hover:border-[#93a0a1] cursor-pointer' }}">
                    <div class="text-[10px] font-bold {{ $paymentMethod === $key ? 'text-race-700' : 'text-teal-800' }}">{{ $method['label'] }}</div>
                    <div class="text-[9px] text-velomutedlight mt-0.5">{{ $method['hint'] }}</div>
                </button>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Countdown batas waktu checkout --}}
        <div class="mt-4 p-3 bg-velosurfacealt border border-veloborder velo-box-3 text-center"
             x-data="{ s: 900 }"
             x-init="setInterval(() => { if (s > 0) s--; }, 1000)">
            <div class="text-[10px] uppercase font-bold text-velomutedlight mb-0.5">Batas Waktu Checkout</div>
            <div class="text-lg font-black font-mono text-race-600 tracking-wider" x-text="String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0')">15:00</div>
        </div>

        <button type="button" wire:click="openPayment" class="mt-4 w-full flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold py-3.5 text-xs velo-box-1 shadow-sm transition">
            <i data-lucide="lock" class="w-3.5 h-3.5 mr-2"></i>
            <span>Lanjutkan Pembayaran</span>
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
