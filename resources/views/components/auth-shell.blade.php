@props(['tab' => 'login'])

{{-- Auth Shell: split-panel card sesuai Login.dc.html screen-print mockup --}}
<div class="vs min-h-screen flex items-center justify-center bg-[var(--paper2)] relative overflow-hidden px-3.5 py-6 sm:px-6 sm:py-10">
    {{-- Halftone dot background --}}
    <div class="dots absolute inset-0 opacity-60"></div>

    {{-- Main auth card --}}
    <div class="card relative w-full max-w-[1000px] grid grid-cols-1 lg:grid-cols-2 overflow-hidden rounded-2xl border-[3px] border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] sm:shadow-[8px_8px_0_var(--ink)]">

        {{-- LEFT: Branding & Illustration (Desktop Only) --}}
        <div class="hidden lg:flex flex-col justify-between relative bg-[var(--butter)] border-r-[2.5px] border-[var(--ink)] p-9 min-h-[640px] overflow-hidden">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="logo inline-flex items-center gap-3 text-2xl relative z-10" style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; letter-spacing: -0.03em; text-decoration: none">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-9 w-auto object-contain" />
                <span class="font-extrabold text-2xl tracking-tight text-[var(--ink)]">Velosha</span>
            </a>

            {{-- Pesepeda atletik yang sedang meminum botol hidrasi (minum.png) --}}
            <img class="float"
                 src="/images/mockup/minum.png"
                 alt="Pesepeda berjersey oranye sedang meminum botol hidrasi"
                 style="position: absolute; left: -10px; top: 90px; width: 480px; height: 480px; object-fit: contain; display: block; filter: drop-shadow(6px 6px 0 rgba(16, 40, 46, 0.12)); pointer-events: none">

            {{-- Tagline --}}
            <h1 class="h3 relative z-10" style="font-size: 30px; line-height: 1.1; max-width: 300px">
                Satu akun untuk semua kalender balapmu.
            </h1>
        </div>

        {{-- RIGHT: Forms (Mobile & Desktop Responsive) --}}
        <div class="p-5 sm:p-8 lg:p-10 flex flex-col gap-5 bg-[var(--card)]">

            {{-- Mobile Brand Header (Hanya di Layar Mobile/Tablet < lg) --}}
            <div class="flex lg:hidden items-center justify-between pb-3 border-b-2 border-dashed border-[var(--ink)]/20 mb-0.5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2" style="text-decoration: none">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                    <span class="font-extrabold text-xl tracking-tight text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 font-bold text-xs text-[var(--mute)] hover:text-[var(--ink)] transition" style="text-decoration: none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                    Beranda
                </a>
            </div>

            {{-- Desktop Back to home --}}
            <a href="{{ route('home') }}" class="hidden lg:inline-flex items-center gap-2 font-bold text-sm hover:text-[var(--orange-d)] transition" style="min-height: 44px; text-decoration: none">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                Kembali ke beranda
            </a>

            {{-- Header & Switcher Tab --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <div>
                    <h2 class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] leading-tight m-0">{{ $tab === 'register' ? 'Daftar' : 'Masuk' }}</h2>
                    <p class="text-[var(--mute)] text-xs sm:text-sm mt-1 leading-snug">
                        {{ $tab === 'register' ? 'Mulai petualangan maraton sepedamu.' : 'Akses tiket dan data pendaftaranmu.' }}
                    </p>
                </div>
                <div class="inline-flex self-start sm:self-auto p-1 border-[2.5px] border-[var(--ink)] rounded-lg bg-[var(--paper2)] gap-1 shrink-0">
                    <a href="{{ route('login') }}"
                       class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm font-bold transition {{ $tab === 'login' ? 'bg-[var(--orange)] border-2 border-[var(--ink)] text-[var(--ink)] shadow-[1.5px_1.5px_0_var(--ink)]' : 'text-[var(--ink)] hover:bg-white' }}"
                       style="text-decoration: none">Masuk</a>
                    <a href="{{ route('register') }}"
                       class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm font-bold transition {{ $tab === 'register' ? 'bg-[var(--orange)] border-2 border-[var(--ink)] text-[var(--ink)] shadow-[1.5px_1.5px_0_var(--ink)]' : 'text-[var(--ink)] hover:bg-white' }}"
                       style="text-decoration: none">Daftar</a>
                </div>
            </div>

            {{-- Session status --}}
            @if (session('status'))
                <div class="px-4 py-3 text-sm bg-[var(--pine-l)] border-2 border-[var(--pine)] rounded-lg font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            {{-- === LOGIN FORM === --}}
            @if ($tab === 'login')
                <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-4 sm:gap-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="label text-xs sm:text-sm font-bold text-[var(--ink)] mb-1 block" for="email">Alamat email</label>
                        <input id="email" name="email" type="email" required autofocus
                               value="{{ old('email') }}" placeholder="peserta@velosha.id"
                               class="input w-full h-11 sm:h-12 px-3.5 text-base sm:text-sm rounded-xl border-2 sm:border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:bg-white @error('email') !border-[var(--red)] !shadow-[3px_3px_0_var(--red)] @enderror">
                        @error('email')
                            <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex justify-between items-baseline mb-1">
                            <label class="label text-xs sm:text-sm font-bold text-[var(--ink)] block m-0" for="password">Kata sandi</label>
                            <a href="{{ route('password.request') }}" class="text-xs sm:text-sm font-bold text-[var(--orange-d)] hover:underline" style="text-decoration: none">Lupa sandi?</a>
                        </div>
                        <input id="password" name="password" type="password" required
                               placeholder="Masukkan kata sandi"
                               class="input w-full h-11 sm:h-12 px-3.5 text-base sm:text-sm rounded-xl border-2 sm:border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:bg-white @error('password') !border-[var(--red)] !shadow-[3px_3px_0_var(--red)] @enderror">
                        @error('password')
                            <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember checkbox --}}
                    <label class="flex gap-2.5 items-center text-xs sm:text-sm font-medium text-[var(--ink)] cursor-pointer select-none">
                        <span class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 border-2 border-[var(--ink)] rounded-md bg-[var(--card)] shrink-0 shadow-[1px_1px_0_var(--ink)]">
                            <input type="checkbox" name="remember" value="1" class="sr-only peer">
                            <svg class="w-3.5 h-3.5 hidden peer-checked:block" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
                        </span>
                        Ingat saya di perangkat ini
                    </label>

                    {{-- Submit --}}
                    <x-ui.submit-button type="submit" variant="orange" loadingText="Masuk ke akun..." :fullWidth="true" size="lg">Masuk ke akun</x-ui.submit-button>
                </form>

            {{-- === REGISTER FORM === --}}
            @else
                <form action="{{ route('register') }}" method="POST" class="flex flex-col gap-4 sm:gap-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label class="label text-xs sm:text-sm font-bold text-[var(--ink)] mb-1 block" for="name">Nama lengkap sesuai KTP/Paspor</label>
                        <input id="name" name="name" type="text" required autofocus
                               value="{{ old('name') }}" placeholder="Budi Pratama"
                               class="input w-full h-11 sm:h-12 px-3.5 text-base sm:text-sm rounded-xl border-2 sm:border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:bg-white @error('name') !border-[var(--red)] !shadow-[3px_3px_0_var(--red)] @enderror">
                        @error('name')
                            <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email & Phone grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                        <div>
                            <label class="label text-xs sm:text-sm font-bold text-[var(--ink)] mb-1 block" for="reg-email">Email aktif</label>
                            <input id="reg-email" name="email" type="email" required
                                   value="{{ old('email') }}" placeholder="budi@example.com"
                                   class="input w-full h-11 sm:h-12 px-3.5 text-base sm:text-sm rounded-xl border-2 sm:border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:bg-white @error('email') !border-[var(--red)] !shadow-[3px_3px_0_var(--red)] @enderror">
                            @error('email')
                                <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="label text-xs sm:text-sm font-bold text-[var(--ink)] mb-1 block" for="phone">Nomor WhatsApp</label>
                            <input id="phone" name="phone" type="tel" required
                                   value="{{ old('phone') }}" placeholder="081234567890"
                                   class="input w-full h-11 sm:h-12 px-3.5 text-base sm:text-sm rounded-xl border-2 sm:border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:bg-white @error('phone') !border-[var(--red)] !shadow-[3px_3px_0_var(--red)] @enderror">
                            @error('phone')
                                <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Password grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-5">
                        <div>
                            <label class="label text-xs sm:text-sm font-bold text-[var(--ink)] mb-1 block" for="reg-password">Kata sandi</label>
                            <input id="reg-password" name="password" type="password" required
                                   placeholder="Minimal 8 karakter"
                                   class="input w-full h-11 sm:h-12 px-3.5 text-base sm:text-sm rounded-xl border-2 sm:border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:bg-white @error('password') !border-[var(--red)] !shadow-[3px_3px_0_var(--red)] @enderror">
                            @error('password')
                                <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="label text-xs sm:text-sm font-bold text-[var(--ink)] mb-1 block" for="password_confirmation">Ulangi sandi</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   placeholder="Konfirmasi sandi" class="input w-full h-11 sm:h-12 px-3.5 text-base sm:text-sm rounded-xl border-2 sm:border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:bg-white">
                        </div>
                    </div>

                    {{-- Terms --}}
                    <p class="text-xs text-[var(--mute)] leading-relaxed">
                        Dengan mendaftar, kamu menyetujui Ketentuan Penyelenggaraan Balap dan Kebijakan Privasi Velosha.
                    </p>

                    {{-- Submit --}}
                    <x-ui.submit-button type="submit" variant="orange" loadingText="Mendaftarkan akun..." :fullWidth="true" size="lg">Buat Akun Velosha</x-ui.submit-button>
                </form>
            @endif

            {{-- Social login divider & buttons (Google Only) --}}
            @if (config('app.demo_login') || config('services.google.client_id'))
                <div class="flex items-center gap-3 text-xs sm:text-sm text-[var(--mute)]">
                    <i class="flex-1 border-t-2 border-[var(--line)]"></i>
                    <span>atau lanjutkan dengan</span>
                    <i class="flex-1 border-t-2 border-[var(--line)]"></i>
                </div>

                <div>
                    <a href="{{ config('services.google.client_id') && Route::has('auth.google') ? route('auth.google') : '/auth/demo/google' }}"
                       class="flex items-center justify-center gap-3 w-full h-12 sm:h-13 border-2 sm:border-[2.5px] border-[var(--ink)] rounded-xl bg-white font-black text-xs sm:text-sm text-[var(--ink)] shadow-[2.5px_2.5px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:bg-[var(--paper)] transition cursor-pointer"
                       style="text-decoration: none">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.85 0-5.27-1.93-6.14-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
