@use('App\Support\EventCardPresenter', 'P')
<div class="bg-[var(--paper)] min-h-[calc(100vh-140px)] pb-16">

    <!-- PAGE HEADER: Pendaftaran & Checkout (Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan) -->
    <section class="relative overflow-hidden" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E); padding: 52px 0;">
        {{-- Elemen Dekorasi Geometris Neobrutalism di Sisi Kanan --}}
        <svg aria-hidden="true" style="position:absolute; top:0; right:0; width:520px; max-width:45%; height:100%; z-index:1; pointer-events:none;" viewBox="0 0 520 280">
            <g stroke="#10282E" stroke-width="3" stroke-linejoin="round">
                <circle cx="90" cy="65" r="9" fill="#DE7119"></circle>
                <rect x="160" y="145" width="20" height="20" rx="3" fill="#1F6F4A" transform="rotate(20 170 155)"></rect>
                <path d="M250 215 l12 -20 l12 20z" fill="#F4BE3F"></path>
                <circle cx="230" cy="55" r="7" fill="#9B3D5A"></circle>
                <rect x="320" y="115" width="18" height="18" rx="3" fill="#F4BE3F" transform="rotate(-15 329 124)"></rect>
                <path d="M420 65 l12 -20 l12 20z" fill="#DE7119"></path>
                <circle cx="475" cy="165" r="8" fill="#1F6F4A"></circle>
            </g>
        </svg>

        <div class="wrap relative z-10">
            <div class="flex items-center gap-2.5 mb-3 flex-wrap">
                <span class="chip font-bold bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                    Formulir Pendaftaran Resmi
                </span>
                @if ($event !== null)
                <span class="chip bg-white text-[var(--ink)] border border-[var(--ink)] font-bold shadow-[2px_2px_0_var(--ink)]">
                    {{ $event->title }}
                </span>
                @endif
            </div>

            <h1 class="font-heading font-black text-4xl sm:text-5xl md:text-6xl text-[var(--ink)] leading-[1.08] tracking-tight mb-4">
                Pendaftaran &amp; Checkout
            </h1>

            <p class="text-base sm:text-lg text-[var(--ink)]/85 leading-relaxed font-medium max-w-2xl">
                Lengkapi data diri dan pilih metode pembayaran resmi via Midtrans untuk mengamankan slot race dan menerbitkan E-Ticket serta nomor BIB Anda.
            </p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if ($error !== '')
        <div class="mb-6 rounded-xl border-2 border-red-500 bg-red-100 text-red-900 text-sm font-bold p-4 shadow-[3px_3px_0_#991b1b] flex items-start gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 mt-0.5 text-red-700"></i>
            <div>{{ $error }}</div>
        </div>
        @endif

        <!-- 3-Step Progress Indicator -->
        <div class="mb-8 rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-5">
            @php
                $stepLabels = [1 => 'Kategori Race', 2 => 'Data Diri & Medis', 3 => 'Pembayaran & Tiket'];
            @endphp
            <div class="flex items-center">
                @foreach ($stepLabels as $number => $label)
                @if ($number > 1)
                <div class="flex-1 h-2 mx-2 sm:mx-4 rounded-full border border-[var(--ink)]/20 transition-all {{ $step >= $number ? 'bg-[#1F6F4A]' : 'bg-gray-200' }}"
                     style="{{ $step >= $number ? 'background-color: #1F6F4A !important;' : '' }}"></div>
                @endif
                <div class="flex items-center gap-2.5 shrink-0">
                    <span class="w-9 h-9 rounded-xl border-2 border-[var(--ink)] flex items-center justify-center text-xs font-black transition-all shadow-[2px_2px_0_var(--ink)] {{ $step > $number ? 'bg-[#1F6F4A] text-white' : ($step === $number ? 'bg-[#F4BE3F] text-[var(--ink)] ring-2 ring-[var(--ink)]' : 'bg-gray-100 text-gray-400') }}"
                          style="{{ $step > $number ? 'background-color: #1F6F4A !important; color: #ffffff !important;' : ($step === $number ? 'background-color: #F4BE3F !important; color: #10282E !important;' : '') }}">
                        @if ($step > $number)
                        <svg class="w-4 h-4 text-white" style="stroke: #ffffff !important;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        @else
                        {{ $number }}
                        @endif
                    </span>
                    <span class="text-xs {{ $step >= $number ? 'text-[var(--ink)] font-black' : 'text-[var(--mute)] font-semibold' }} hidden sm:block">{{ $label }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2.5 sm:hidden px-1">
                @foreach ($stepLabels as $number => $label)
                <span class="text-[10px] {{ $step === $number ? 'text-[var(--ink)] font-black' : 'text-[var(--mute)]' }}">{{ $label }}</span>
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

    <!-- Midtrans Snap Simulation Modal -->
    @include('livewire.checkout.payment-modal')

    {{-- Midtrans Snap Popup with 100% Blurred Backdrop --}}
    <div id="snap-embed-modal" class="fixed inset-0 z-[999999] bg-[#10282E]/65 backdrop-blur-md hidden transition-all duration-300 flex items-center justify-center p-4">
        <div class="relative w-full max-w-[460px] bg-white rounded-2xl border-[3.5px] border-[var(--ink)] shadow-[8px_8px_0_var(--ink)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 bg-[var(--butter)] border-b-[2.5px] border-[var(--ink)]">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-[var(--orange)] border border-[var(--ink)]"></span>
                    <span class="font-heading font-black text-sm text-[var(--ink)] tracking-tight">Pembayaran Resmi Midtrans</span>
                </div>
                <button type="button" id="close-snap-modal-btn" class="w-7 h-7 rounded-lg border-2 border-[var(--ink)] bg-white hover:bg-red-100 text-[var(--ink)] flex items-center justify-center text-xs font-black cursor-pointer shadow-[1.5px_1.5px_0_var(--ink)] transition">✕</button>
            </div>
            <div id="snap-container" class="w-full min-h-[540px] flex items-center justify-center bg-[#FAF9F5]">
                <div id="snap-loading-state" class="text-xs font-bold text-[var(--mute)] p-8 text-center flex flex-col items-center gap-2">
                    <svg class="animate-spin h-6 w-6 text-[var(--orange)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Memuat gerbang pembayaran Midtrans...</span>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-midtrans-snap', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                const token = payload?.token;
                const regId = payload?.registrationId;
                const redirectUrl = payload?.redirectUrl;
                const modal = document.getElementById('snap-embed-modal');
                const container = document.getElementById('snap-container');
                const closeBtn = document.getElementById('close-snap-modal-btn');

                if (closeBtn) {
                    closeBtn.onclick = () => {
                        if (modal) modal.classList.add('hidden');
                    };
                }

                if (window.snap && token) {
                    if (modal) modal.classList.remove('hidden');

                    try {
                        if (typeof window.snap.embed === 'function') {
                            container.innerHTML = '';
                            window.snap.embed(token, {
                                embedId: 'snap-container',
                                onSuccess: function(result) {
                                    if (modal) modal.classList.add('hidden');
                                    window.location.href = '/ticket/' + regId;
                                },
                                onPending: function(result) {
                                    if (modal) modal.classList.add('hidden');
                                    window.location.href = '/ticket/' + regId;
                                },
                                onError: function(result) {
                                    if (modal) modal.classList.add('hidden');
                                    alert('Pembayaran tidak berhasil atau dibatalkan.');
                                },
                                onClose: function() {
                                    if (modal) modal.classList.add('hidden');
                                }
                            });
                            return;
                        }
                    } catch (err) {
                        console.warn('snap.embed failed, falling back to snap.pay', err);
                    }

                    // Fallback ke snap.pay
                    window.snap.pay(token, {
                        onSuccess: function(result) {
                            if (modal) modal.classList.add('hidden');
                            window.location.href = '/ticket/' + regId;
                        },
                        onPending: function(result) {
                            if (modal) modal.classList.add('hidden');
                            window.location.href = '/ticket/' + regId;
                        },
                        onError: function(result) {
                            if (modal) modal.classList.add('hidden');
                            alert('Pembayaran tidak berhasil atau dibatalkan.');
                        },
                        onClose: function() {
                            if (modal) modal.classList.add('hidden');
                        }
                    });
                } else if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    if (modal) modal.classList.add('hidden');
                    alert('Gagal memuat jendela pembayaran Midtrans. Silakan coba kembali.');
                }
            });
        });
    </script>
</div>
