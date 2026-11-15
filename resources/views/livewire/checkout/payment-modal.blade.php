@use('App\Support\EventCardPresenter', 'P')
{{-- Modal konfirmasi pembayaran simulasi Midtrans Snap (metode dipilih di sidebar) --}}
@if (config('payments.simulation') && $showPaymentModal && $registration !== null)
<div class="fixed inset-0 z-50 overflow-y-auto bg-[#10282E]/60 backdrop-blur-md flex items-center justify-center p-4" wire:click="closePayment">
    <div class="bg-[#FFFDF8] max-w-md w-full rounded-2xl border-[2.5px] border-[#10282E] shadow-[6px_6px_0_#10282E] overflow-hidden" @click.stop>
        <!-- Snap Header -->
        <div class="bg-[var(--orange)] text-[var(--ink)] px-5 py-3.5 flex justify-between items-center border-b-[2.5px] border-[#10282E]">
            <div class="flex items-center space-x-2">
                <span class="font-extrabold text-sm tracking-tight text-white">MIDTRANS SNAP</span>
                <span class="text-[10px] bg-white/20 text-white px-2 py-0.5 rounded border border-white/40 font-mono font-bold">SIMULATION</span>
            </div>
            <button type="button" wire:click="closePayment" class="text-white/80 hover:text-white cursor-pointer" aria-label="Tutup">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Snap Body -->
        <div class="p-6 space-y-4" @click.stop>
            <div class="text-center pb-4 border-b-2 border-dashed border-[#10282E]/20">
                <div class="text-xs font-semibold text-[#10282E]/70">Order ID: VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="text-2xl font-black text-[#10282E] mt-1">Rp {{ P::angka((float) $this->total()) }}</div>
                <div class="text-xs text-[#10282E]/70 mt-0.5 font-medium">Velosha &mdash; {{ $event?->title ?? 'Velosha Race' }}</div>
            </div>

            <!-- Detail pembayaran sesuai metode terpilih (simulasi) -->
            @if (in_array($paymentMethod, ['bca_va', 'bri_va', 'bni_va'], true))
            @php
                $vaPrefixes = ['bca_va' => '8808', 'bri_va' => '2600', 'bni_va' => '9888'];
                $vaBankLabels = ['bca_va' => 'BCA', 'bri_va' => 'BRI', 'bni_va' => 'BNI'];
                $vaNumber = $vaPrefixes[$paymentMethod].str_pad((string) $registration->id, 12, '0', STR_PAD_LEFT);
            @endphp
            <div class="p-4 bg-[#F8E3B5] border-2 border-[#10282E] rounded-xl text-center shadow-[3px_3px_0_#10282E]">
                <div class="text-[11px] font-bold text-[#10282E] mb-2">Transfer ke Virtual Account {{ $vaBankLabels[$paymentMethod] }} ini:</div>
                <div class="bg-white border-2 border-[#10282E] rounded-lg py-3 font-mono text-base font-black tracking-wider text-[#10282E] shadow-[2px_2px_0_#10282E]">{{ wordwrap($vaNumber, 4, ' ', true) }}</div>
                <button type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $vaNumber }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        class="mt-3 w-full border-2 border-[#10282E] bg-white hover:bg-[#F8E3B5] text-[#10282E] text-xs font-bold py-2 rounded-lg shadow-[2px_2px_0_#10282E] transition active:translate-y-0.5 cursor-pointer">
                    <span x-text="copied ? 'Nomor tersalin!' : 'Salin Nomor VA'">Salin Nomor VA</span>
                </button>
                <div class="text-[10px] text-[#10282E]/70 mt-2 font-medium">Nomor VA dibuat khusus untuk order ini. Bayar penuh agar verifikasi otomatis.</div>
            </div>
            @elseif ($paymentMethod === 'mandiri')
            <div class="p-4 bg-[#F8E3B5] border-2 border-[#10282E] rounded-xl text-center shadow-[3px_3px_0_#10282E]">
                <div class="text-[11px] font-bold text-[#10282E] mb-2">Bayar Mandiri Bill Payment dengan Bill Key:</div>
                @php($billKey = '9080'.str_pad((string) $registration->id, 12, '0', STR_PAD_LEFT))
                <div class="bg-white border-2 border-[#10282E] rounded-lg py-3 font-mono text-base font-black tracking-wider text-[#10282E] shadow-[2px_2px_0_#10282E]">{{ wordwrap($billKey, 3, ' ', true) }}</div>
                <button type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $billKey }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        class="mt-3 w-full border-2 border-[#10282E] bg-white hover:bg-[#F8E3B5] text-[#10282E] text-xs font-bold py-2 rounded-lg shadow-[2px_2px_0_#10282E] transition active:translate-y-0.5 cursor-pointer">
                    <span x-text="copied ? 'Bill key tersalin!' : 'Salin Bill Key'">Salin Bill Key</span>
                </button>
                <div class="text-[10px] text-[#10282E]/70 mt-2 font-medium">Buka Livin' by Mandiri &rarr; Bayar &rarr; Multipayment &rarr; masukkan company code 70012.</div>
            </div>
            @else
            <div class="p-4 bg-[#F8E3B5] border-2 border-[#10282E] rounded-xl text-center shadow-[3px_3px_0_#10282E]">
                <div class="text-[11px] font-bold text-[#10282E] mb-2">Pindai QRIS untuk menyelesaikan simulasi:</div>
                <div class="inline-block p-2 bg-white border-2 border-[#10282E] rounded-lg shadow-[2px_2px_0_#10282E]">
                    <img src="{{ \App\Support\QrCodeService::uri('MIDTRANS-VELOSHA-REG-'.str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT).'-PAID') }}" alt="QR Code Midtrans" class="w-32 h-32 mx-auto">
                </div>
            </div>
            @endif

            <!-- Final Confirm Button -->
            <x-ui.submit-button
                type="button"
                wire:click="simulatePaid"
                target="simulatePaid"
                variant="pine"
                loadingText="Memproses pembayaran..."
                :fullWidth="true">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                Simulasikan Pembayaran Berhasil
            </x-ui.submit-button>

            <p class="text-[10px] text-[#10282E]/60 text-center font-medium">
                Mode simulasi: tidak ada transaksi nyata yang diproses. Integrasi Midtrans asli menyusul.
            </p>
        </div>
    </div>
</div>
@endif
