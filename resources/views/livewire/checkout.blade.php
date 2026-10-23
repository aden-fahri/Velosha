@use('App\Support\EventCardPresenter', 'P')
<div class="bg-velosurfacealt min-h-[calc(100vh-140px)]">

    {{-- Header abu-abu + kontur topo horizontal (seragam dengan halaman lain) --}}
    <x-page-header
        size="compact"
        width="max-w-5xl"
        eyebrow="Formulir Pendaftaran Resmi"
        title="Pendaftaran & Konfirmasi Tiket"
        :description="$event !== null ? 'Lengkapi data diri dan pilih metode pembayaran untuk menerbitkan E-Ticket serta nomor BIB resmi Anda.' : 'Lengkapi data diri resmi peserta maraton untuk alokasi nomor BIB dan asuransi medis.'"
        :chips="$event !== null ? array_values(array_filter([
            ['icon' => 'flag', 'label' => $event->title],
            ['icon' => 'calendar', 'label' => P::tanggalLengkap($event->event_date)],
            $event->location_name ? ['icon' => 'map-pin', 'label' => $event->location_name] : null,
            ['icon' => 'timer', 'label' => 'Timing · RFID Chip'],
        ])) : []"
    />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if ($error !== '')
        <div class="mb-6 border border-red-300 bg-red-50 text-red-700 text-xs p-3.5 velo-box-1 flex items-start space-x-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
            <span>{{ $error }}</span>
        </div>
        @endif

        <!-- 3-Step Progress Indicator (circle + connector) -->
        <div class="mb-8 bg-white border border-veloborder velo-box-1 p-5">
            @php
                $stepLabels = [1 => 'Kategori Race', 2 => 'Data Diri & Medis', 3 => 'Pembayaran & Tiket'];
            @endphp
            <div class="flex items-center">
                @foreach ($stepLabels as $number => $label)
                @if ($number > 1)
                <div class="flex-1 h-0.5 mx-2 sm:mx-4 {{ $step >= $number ? 'bg-race-500' : 'bg-veloborder' }}"></div>
                @endif
                <div class="flex items-center gap-2.5 shrink-0">
                    <span class="w-8 h-8 flex items-center justify-center velo-box-3 text-xs font-black transition {{ $step > $number ? 'bg-emerald-600 text-[var(--ink)]' : ($step === $number ? 'bg-[var(--orange)] text-[var(--ink)] ring-4 ring-race-100' : 'bg-velosurface text-velomutedlight border border-veloborder') }}">
                        @if ($step > $number)
                        <i data-lucide="check" class="w-4 h-4"></i>
                        @else
                        {{ $number }}
                        @endif
                    </span>
                    <span class="text-xs {{ $step >= $number ? 'text-teal-900 font-extrabold' : 'text-velomuted font-medium' }} hidden sm:block">{{ $label }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 sm:hidden">
                @foreach ($stepLabels as $number => $label)
                <span class="text-[10px] {{ $step === $number ? 'text-race-600 font-bold' : 'text-velomuted' }}">{{ $label }}</span>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Form Input (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">
                @include('livewire.checkout.step-1-category')
                @include('livewire.checkout.step-2-medical')
                @include('livewire.checkout.step-3-review')
            </div>

            <!-- Right: Order Summary & Midtrans Payment Trigger (4 Cols) -->
            @include('livewire.checkout.order-summary')

        </div>

    </div>
</div>

<!-- Midtrans Snap Simulation Modal -->
@include('livewire.checkout.payment-modal')
