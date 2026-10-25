@props(['tab' => 'login'])

<div class="min-h-[calc(100vh-96px)] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-velosurfacealt">
    <div class="max-w-2xl w-full mx-auto bg-white border border-veloborder velo-box-1 shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12">

        <!-- Left Side: Editorial Photography -->
        <div class="hidden lg:block lg:col-span-5 relative bg-teal-900 text-white">
            <img src="/images/auth-cyclist.jpg"
                 alt="Velosha Cyclist"
                 class="w-full h-full object-cover object-center opacity-40 filter contrast-110">
            <div class="absolute inset-0 bg-gradient-to-t from-teal-900 via-teal-900/60 to-transparent"></div>

            <img src="/images/group-1.png"
                 alt="Ilustrasi Velosha"
                 class="absolute inset-0 w-full h-full object-cover object-left z-10 pointer-events-none">

            <div class="absolute bottom-8 left-8 right-8 z-10">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-10 w-auto object-contain invert mb-4" />
                <h3 class="text-lg font-black text-white leading-tight">
                    Satu Akun untuk Seluruh Kalender Balap Anda.
                </h3>
            </div>
        </div>

        <!-- Right Side: Auth Forms & Social Logins -->
        <div class="lg:col-span-7 p-6 sm:p-7 flex flex-col justify-center">

            <!-- Back to Landing -->
            <a href="{{ route('home') }}" class="inline-flex self-start items-center gap-1.5 border border-veloborderstrong bg-white hover:bg-velosurfacealt px-3 py-1.5 text-xs font-semibold text-teal-700 velo-box-1 transition mb-5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Beranda</span>
            </a>

            <!-- Auth Switcher Header -->
            <div class="flex items-center justify-between border-b border-veloborder pb-4 mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">
                        {{ $tab === 'register' ? 'Daftar Akun Baru' : 'Masuk ke Velosha' }}
                    </h2>
                    <p class="text-xs text-velomutedlight mt-1">
                        {{ $tab === 'register'
                            ? 'Mulai petualangan maraton sepeda Anda bersama Velosha'
                            : 'Akses tiket dan data pendaftaran Anda' }}
                    </p>
                </div>
                <div class="flex border border-veloborderstrong velo-box-3 p-0.5 bg-velosurfacealt text-xs">
                    <a href="{{ route('login') }}"
                       class="px-3 py-1 velo-box-3 {{ $tab === 'login' ? 'bg-white text-race-600 font-bold shadow-xs' : 'font-medium text-velomutedlight hover:text-race-600' }}">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-3 py-1 velo-box-3 {{ $tab === 'register' ? 'bg-white text-race-600 font-bold shadow-xs' : 'font-medium text-velomutedlight hover:text-race-600' }}">
                        Daftar
                    </a>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-4 px-3 py-2 text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 velo-box-1">
                    {{ session('status') }}
                </div>
            @endif

            @if ($tab === 'login')
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-xs font-semibold text-teal-700 mb-1">Alamat Email</label>
                        <input id="email" type="email" name="email" required autofocus
                               value="{{ old('email') }}" placeholder="cyclist@velosha.id"
                               class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('email') border-red-600 @enderror">
                        @error('email')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-xs font-semibold text-teal-700">Kata Sandi</label>
                            <a href="{{ route('password.request') }}" class="text-[11px] text-race-600 hover:underline">Lupa sandi?</a>
                        </div>
                        <input id="password" type="password" name="password" required
                               class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('password') border-red-600 @enderror">
                        @error('password')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember" value="1"
                               class="h-3.5 w-3.5 text-teal-900 focus:ring-race-500 border-veloborderstrong rounded-[1px]">
                        <label for="remember" class="ml-2 block text-xs text-velomutedlight">Ingat sesi saya di perangkat ini</label>
                    </div>

                    <button type="submit" class="w-full bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold py-2.5 text-xs velo-box-1 shadow-sm transition">
                        Masuk ke Akun
                    </button>
                </form>
            @else
                <form action="{{ route('register') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-semibold text-teal-700 mb-1">Nama Lengkap Sesuai KTP/Paspor</label>
                        <input id="name" type="text" name="name" required autofocus value="{{ old('name') }}"
                               placeholder="Budi Pratama"
                               class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('name') border-red-600 @enderror">
                        @error('name')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="reg-email" class="block text-xs font-semibold text-teal-700 mb-1">Email Aktif</label>
                            <input id="reg-email" type="email" name="email" required value="{{ old('email') }}"
                                   placeholder="budi@example.com"
                                   class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('email') border-red-600 @enderror">
                            @error('email')
                                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-teal-700 mb-1">Nomor WhatsApp (Untuk Notifikasi Race)</label>
                            <input id="phone" type="tel" name="phone" required value="{{ old('phone') }}"
                                   placeholder="081234567890"
                                   class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('phone') border-red-600 @enderror">
                            @error('phone')
                                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="reg-password" class="block text-xs font-semibold text-teal-700 mb-1">Kata Sandi</label>
                            <input id="reg-password" type="password" name="password" required
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('password') border-red-600 @enderror">
                            @error('password')
                                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-teal-700 mb-1">Ulangi Sandi</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   placeholder="Konfirmasi sandi"
                                   class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
                        </div>
                    </div>

                    <div class="text-[11px] text-velomutedlight pt-1">
                        Dengan mendaftar, Anda menyetujui Ketentuan Penyelenggaraan Balap dan Kebijakan Privasi Velosha.
                    </div>

                    <button type="submit" class="w-full bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold py-2.5 text-xs velo-box-1 shadow-sm transition mt-2">
                        Buat Akun Velosha
                    </button>
                </form>
            @endif

            @if (config('app.demo_login'))
                <!-- Divider -->
                <div class="relative my-4">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-veloborder"></div></div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-velomuted">atau lanjutkan dengan</span>
                    </div>
                </div>

                <!-- Social Login Buttons (mode demo — Socialite asli menyusul) -->
                <div class="space-y-2">
                    <a href="/auth/demo/google" class="w-full flex items-center justify-center px-4 py-2.5 border border-veloborderstrong bg-white hover:bg-velosurfacealt text-xs font-semibold text-teal-700 velo-box-1 transition">
                        <svg class="w-4 h-4 mr-2.5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.85 0-5.27-1.93-6.14-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>

                    <a href="/auth/demo/facebook" class="w-full flex items-center justify-center px-4 py-2.5 border border-veloborderstrong bg-white hover:bg-velosurfacealt text-xs font-semibold text-teal-700 velo-box-1 transition">
                        <svg class="w-4 h-4 mr-2.5 text-[#1877F2] fill-current" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Lanjutkan dengan Facebook</span>
                    </a>

                    <a href="/auth/demo/tiktok" class="w-full flex items-center justify-center px-4 py-2.5 border border-veloborderstrong bg-white hover:bg-velosurfacealt text-xs font-semibold text-teal-700 velo-box-1 transition">
                        <svg class="w-4 h-4 mr-2.5 text-black fill-current" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.97v7.54c-.01 2.3-.92 4.57-2.61 6.13-2.12 2-5.21 2.64-7.88 1.61-2.67-1.03-4.54-3.57-4.68-6.44-.19-3.23 1.93-6.2 5.06-6.98 1.05-.27 2.15-.24 3.2-.01v4.18c-.68-.23-1.44-.24-2.14-.06-1.15.28-2.02 1.28-2.12 2.46-.14 1.45.85 2.77 2.27 3.02 1.48.27 2.98-.6 3.37-2.02.11-.42.17-.86.17-1.3V.02h-1.61z"/>
                        </svg>
                        <span>Lanjutkan dengan TikTok</span>
                    </a>
                </div>
            @endif

        </div>

    </div>
</div>
