@use('App\Support\EventCardPresenter', 'P')
{{-- Modal konfirmasi pembayaran simulasi Midtrans Snap (metode dipilih di sidebar) --}}
@if (config('payments.simulation') && $showPaymentModal && $registration !== null)
<div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" wire:click="closePayment">
    <div class="bg-white max-w-md w-full velo-box-1 border border-veloborderstrong shadow-2xl overflow-hidden velo-modal-enter" @click.stop>
        <!-- Snap Header -->
        <div class="bg-[var(--orange)] text-[var(--ink)] px-5 py-3.5 flex justify-between items-center border-b border-race-500">
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
                <div class="text-xs text-velomutedlight">Order ID: VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="text-2xl font-extrabold text-teal-900 mt-1">Rp {{ P::angka((float) $this->total()) }}</div>
                <div class="text-xs text-velomutedlight mt-0.5">Velosha &mdash; {{ $event?->title ?? 'Velosha Race' }}</div>
            </div>

            <!-- Detail pembayaran sesuai metode terpilih (simulasi) -->
            @if (in_array($paymentMethod, ['bca_va', 'bri_va', 'bni_va'], true))
            @php
                $vaPrefixes = ['bca_va' => '8808', 'bri_va' => '2600', 'bni_va' => '9888'];
                $vaBankLabels = ['bca_va' => 'BCA', 'bri_va' => 'BRI', 'bni_va' => 'BNI'];
                $vaNumber = $vaPrefixes[$paymentMethod].str_pad((string) $registration->id, 12, '0', STR_PAD_LEFT);
            @endphp
            <div class="p-4 bg-velosurfacealt border border-veloborder velo-box-3 text-center">
                <div class="text-[11px] font-semibold text-teal-700 mb-2">Transfer ke Virtual Account {{ $vaBankLabels[$paymentMethod] }} ini:</div>
                <div class="bg-white border border-veloborderstrong velo-box-1 py-3 font-mono text-base font-bold tracking-wider text-teal-900">{{ wordwrap($vaNumber, 4, ' ', true) }}</div>
                <button type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $vaNumber }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        class="mt-3 w-full border border-veloborderstrong bg-white hover:bg-velosurface text-teal-800 text-xs font-bold py-2 velo-box-3 transition">
                    <span x-text="copied ? 'Nomor tersalin!' : 'Salin Nomor VA'">Salin Nomor VA</span>
                </button>
                <div class="text-[10px] text-velomuted mt-2">Nomor VA dibuat khusus untuk order ini. Bayar penuh agar verifikasi otomatis.</div>
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
                <div class="text-[10px] text-velomuted mt-2">Buka Livin' by Mandiri &rarr; Bayar &rarr; Multipayment &rarr; masukkan company code 70012.</div>
            </div>
            @else
            <div class="p-4 bg-velosurfacealt border border-veloborder velo-box-3 text-center">
                <div class="text-[11px] font-semibold text-teal-700 mb-2">Pindai QRIS untuk menyelesaikan simulasi:</div>
                <div class="inline-block p-2 bg-white border border-veloborderstrong velo-box-1">
                    <img src="{{ \App\Support\QrCodeService::uri('MIDTRANS-VELOSHA-REG-'.str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT).'-PAID') }}" alt="QR Code Midtrans" class="w-32 h-32 mx-auto">
                </div>
            </div>
            @endif

            <!-- Final Confirm Button -->
            <button type="button" wire:click="simulatePaid" wire:loading.attr="disabled" class="w-full flex items-center justify-center bg-emerald-800 hover:bg-emerald-900 text-white font-bold py-3 text-xs velo-box-1 shadow-sm transition disabled:opacity-60">
                <span wire:loading.remove wire:target="simulatePaid" class="flex items-center">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Simulasikan Pembayaran Berhasil
                </span>
                <span wire:loading wire:target="simulatePaid">Memproses pembayaran...</span>
            </button>

            <p class="text-[10px] text-velomuted text-center">
                Mode simulasi: tidak ada transaksi nyata yang diproses. Integrasi Midtrans asli menyusul.
            </p>
        </div>
    </div>
</div>
@endif
