@extends('layouts.app', ['title' => 'Gate Scanner — ' . $event->title, 'nav' => false])

@section('content')
<div x-data="{
    code: '',
    loading: false,
    lastResult: null,
    checkedIn: {{ $checkedInCount }},
    total: {{ $totalRegistrations }},
    cameraActive: false,
    scanner: null,
    audioAllowed: true,

    init() {
        this.$nextTick(() => {
            this.initScanner();
        });
    },

    playSound(type) {
        if (!this.audioAllowed) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
                osc.start();
                osc.stop(ctx.currentTime + 0.2);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            }
        } catch (e) {}
    },

    initScanner() {
        if (typeof Html5Qrcode === 'undefined') return;
        try {
            this.scanner = new Html5Qrcode('qr-reader');
            this.startCamera();
        } catch (e) {
            console.error('QR Scanner init error:', e);
        }
    },

    startCamera() {
        if (!this.scanner) return;
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        this.scanner.start(
            { facingMode: 'environment' },
            config,
            (decodedText) => {
                this.verifyCode(decodedText);
            },
            (error) => {}
        ).then(() => {
            this.cameraActive = true;
        }).catch((err) => {
            this.cameraActive = false;
        });
    },

    stopCamera() {
        if (this.scanner && this.cameraActive) {
            this.scanner.stop().then(() => {
                this.cameraActive = false;
            }).catch(() => {});
        }
    },

    verifyCode(val) {
        const queryCode = (val || this.code || '').trim();
        if (!queryCode || this.loading) return;

        this.loading = true;
        this.code = '';

        fetch('{{ route('organizations.events.scanner.verify', ['organization' => $organization->slug, 'event' => $event->slug]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: queryCode, confirm: true })
        })
        .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
        .then(({ status, ok, body }) => {
            this.loading = false;
            this.lastResult = body;

            if (ok && body.success) {
                this.playSound('success');
                if (body.status === 'checked_in_now') {
                    this.checkedIn++;
                }
            } else {
                this.playSound('error');
            }
        })
        .catch(err => {
            this.loading = false;
            this.playSound('error');
            this.lastResult = {
                success: false,
                status: 'network_error',
                message: 'Gagal terhubung ke server. Periksa jaringan Anda.'
            };
        });
    },

    resetResult() {
        this.lastResult = null;
        this.code = '';
        this.$nextTick(() => {
            const inp = document.getElementById('manual-bib-input');
            if (inp) inp.focus();
        });
    }
}" class="min-h-screen bg-[#F3E7CF] text-[var(--ink)] flex flex-col">

    {{-- ==================== TOP NAVIGATION BAR ==================== --}}
    <header class="bg-[#10282E] text-white border-b-4 border-[var(--mustard)] px-4 sm:px-6 py-3.5 sticky top-0 z-30 shadow-md">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border-2 border-white/30 bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                    <span>Kembali ke Dasbor</span>
                </a>
                <div class="hidden sm:block h-5 w-[1px] bg-white/20"></div>
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-[var(--mustard)]">GATE SCANNER HARIAN (RPC &amp; RACEPACK)</div>
                    <h1 class="text-sm sm:text-base font-black truncate max-w-xs sm:max-w-md font-heading text-white">
                        {{ $event->title }}
                    </h1>
                </div>
            </div>

            {{-- Counter Kehadiran Realtime --}}
            <div class="flex items-center gap-3 shrink-0">
                <div class="text-right">
                    <div class="text-[10px] font-black uppercase text-white/70">PROGRES CHECK-IN</div>
                    <div class="text-xs sm:text-sm font-black font-mono text-[var(--mustard)]">
                        <span x-text="checkedIn"></span> / <span x-text="total"></span> Peserta
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/10 border-2 border-white/20 flex items-center justify-center font-black text-xs font-mono text-white">
                    <span x-text="total > 0 ? Math.round((checkedIn / total) * 100) + '%' : '0%'"></span>
                </div>
            </div>
        </div>
    </header>

    {{-- ==================== MAIN CONTENT ==================== --}}
    <main class="max-w-6xl mx-auto w-full p-4 sm:p-6 flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- KOLOM KIRI: KAMERA SCANNER & INPUT MANUAL (5 Kolom) --}}
        <div class="lg:col-span-5 space-y-5">
            {{-- Kamera QR Viewport --}}
            <div class="bg-white border-[3px] border-[var(--ink)] rounded-2xl p-5 shadow-[5px_5px_0_var(--ink)] space-y-4">
                <div class="flex items-center justify-between pb-2 border-b-2 border-[var(--line)]">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" :class="cameraActive ? 'bg-[var(--pine)] animate-pulse' : 'bg-[var(--red)]'"></span>
                        <h2 class="font-heading font-black text-xs uppercase tracking-wider text-[var(--ink)]">Kamera Pemindai QR</h2>
                    </div>
                    <button type="button" @click="cameraActive ? stopCamera() : startCamera()"
                            class="text-xs font-bold px-2.5 py-1 rounded-md border border-[var(--ink)] shadow-[1px_1px_0_var(--ink)] transition"
                            :class="cameraActive ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200'"
                            x-text="cameraActive ? 'Jeda Kamera' : 'Buka Kamera'">
                    </button>
                </div>

                {{-- Box Viewfinder --}}
                <div class="relative bg-[#10282E] rounded-xl overflow-hidden border-2 border-[var(--ink)] aspect-square flex items-center justify-center shadow-inner">
                    <div id="qr-reader" class="w-full h-full"></div>
                    <div x-show="!cameraActive" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-white/80 space-y-3 bg-[#10282E]/95">
                        <svg class="w-12 h-12 text-[var(--mustard)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <p class="text-xs font-medium max-w-xs">Kamera tidak aktif atau izin kamera belum diberikan. Anda dapat mengaktifkan kamera atau gunakan input manual nomor BIB di bawah.</p>
                        <button type="button" @click="startCamera()" class="btn btn-mustard btn-sm font-black">
                            Izinkan &amp; Aktifkan Kamera
                        </button>
                    </div>
                </div>

                <div class="text-[11px] text-[var(--mute)] font-medium text-center">
                    Arahkan kamera ke QR Code pada E-Ticket fisik atau layar HP peserta.
                </div>
            </div>

            {{-- Input Manual BIB / Kode Tiket --}}
            <div class="bg-white border-[3px] border-[var(--ink)] rounded-2xl p-5 shadow-[5px_5px_0_var(--ink)] space-y-3">
                <label for="manual-bib-input" class="block font-heading font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                    Atau Masukkan Nomor BIB / Kode Manual
                </label>
                <form @submit.prevent="verifyCode()" class="flex gap-2">
                    <input type="text"
                           id="manual-bib-input"
                           x-model="code"
                           placeholder="Contoh: VLS-GF-0001"
                           class="flex-1 px-3.5 py-2.5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-mono font-bold text-xs uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-[var(--mustard)]">
                    <button type="submit"
                            :disabled="loading"
                            class="btn btn-mustard btn-sm font-black shrink-0 px-4">
                        <span x-show="!loading">Verifikasi &rarr;</span>
                        <span x-show="loading">Memeriksa...</span>
                    </button>
                </form>
                <p class="text-[11px] text-[var(--mute)] font-medium">Gunakan jika layar peserta retak, baterai HP habis, atau barcode tidak terbaca kamera.</p>
            </div>
        </div>

        {{-- KOLOM KANAN: HASIL VERIFIKASI & CHECKLIST (7 Kolom) --}}
        <div class="lg:col-span-7">

            {{-- 1. State Default: Siap Memindai --}}
            <template x-if="!lastResult">
                <div class="bg-white border-[3px] border-[var(--ink)] rounded-2xl p-10 text-center shadow-[5px_5px_0_var(--ink)] space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-[var(--butter)] border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] flex items-center justify-center mx-auto text-[var(--orange)]">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-xl text-[var(--ink)]">Gate Siap Memindai</h3>
                        <p class="text-xs text-[var(--mute)] font-medium mt-1 max-w-sm mx-auto">
                            Pindai QR code e-ticket peserta atau ketikkan nomor BIB untuk memeriksa keabsahan pendaftaran dan memproses check-in racepack.
                        </p>
                    </div>
                    <div class="pt-3 border-t-2 border-[var(--line)] flex justify-center items-center gap-6 text-xs font-bold text-[var(--ink)]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[var(--pine)]"></span>
                            <span>Anti Tiket Ganda</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[var(--orange)]"></span>
                            <span>Audio Feedback Aktif</span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- 2. State Sukses: BERHASIL CHECK-IN --}}
            <template x-if="lastResult && lastResult.status === 'checked_in_now'">
                <div class="bg-white border-[3.5px] border-[var(--ink)] rounded-2xl p-6 sm:p-7 shadow-[6px_6px_0_var(--ink)] space-y-6 animate-fade-in">
                    <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-[#E8F5E9] shadow-[3px_3px_0_var(--ink)] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[var(--pine)] text-white border-2 border-[var(--ink)] flex items-center justify-center font-black shadow-[2px_2px_0_var(--ink)] shrink-0">
                                &check;
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase text-[#1F6F4A] tracking-wider">VERIFIKASI SUKSES</div>
                                <h3 class="font-heading font-black text-base text-[var(--ink)]" x-text="lastResult.message"></h3>
                            </div>
                        </div>
                        <span class="chip font-mono text-xs bg-white border border-[var(--ink)] font-bold shadow-[1px_1px_0_var(--ink)]" x-text="lastResult.data.checked_in_at + ' WIB'"></span>
                    </div>

                    {{-- Data Kartu Peserta --}}
                    <div class="p-5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] shadow-[3px_3px_0_var(--ink)] grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2 flex items-center justify-between pb-3 border-b-2 border-[var(--line)]">
                            <div>
                                <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">NOMOR BIB RESMI</div>
                                <div class="font-heading font-black text-3xl sm:text-4xl text-[var(--orange)] tracking-tight font-mono" x-text="lastResult.data.bib_number"></div>
                            </div>
                            <span class="chip font-black bg-[var(--mustard)] text-[var(--ink)] border-2 border-[var(--ink)] text-xs shadow-[2px_2px_0_var(--ink)]" x-text="lastResult.data.category_name"></span>
                        </div>

                        <div>
                            <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">NAMA LENGKAP PESERTA</div>
                            <div class="font-heading font-black text-lg text-[var(--ink)] mt-0.5" x-text="lastResult.data.participant_name"></div>
                            <div class="text-xs text-[var(--mute)] font-semibold mt-0.5" x-text="'Komunitas: ' + lastResult.data.club"></div>
                        </div>

                        <div>
                            <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">GOLONGAN DARAH &amp; MEDIS</div>
                            <div class="inline-flex items-center gap-2 mt-1">
                                <span class="px-2.5 py-1 rounded-lg border-2 border-[var(--ink)] bg-red-100 text-red-700 font-black text-xs shadow-[1px_1px_0_var(--ink)]" x-text="'Gol. ' + lastResult.data.blood_type"></span>
                            </div>
                        </div>

                        <div class="sm:col-span-2 pt-2 border-t border-[var(--line)]">
                            <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">KONTAK DARURAT (ICE)</div>
                            <div class="font-bold text-sm text-[var(--ink)] mt-0.5" x-text="lastResult.data.emergency_contact"></div>
                        </div>
                    </div>

                    {{-- Checklist Serah Terima Racepack Fisik --}}
                    <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--butter)] space-y-2.5">
                        <div class="text-xs font-black uppercase tracking-wider text-[var(--ink)]">Checklist Meja Marshall:</div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs font-bold">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="rounded border-2 border-[var(--ink)] text-[var(--orange)] focus:ring-0">
                                <span>Nomor BIB + Peniti</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="rounded border-2 border-[var(--ink)] text-[var(--orange)] focus:ring-0">
                                <span>Stiker RFID Sepeda</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="rounded border-2 border-[var(--ink)] text-[var(--orange)] focus:ring-0">
                                <span>Jersey &amp; Goodie Bag</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="resetResult()" class="btn btn-block btn-mustard font-black py-3.5 text-xs shadow-[3px_3px_0_var(--ink)]">
                            <span>Scan Peserta Berikutnya &rarr;</span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- 3. State Peringatan: SUDAH PERNAH CHECK-IN (Double Scan Protection) --}}
            <template x-if="lastResult && lastResult.status === 'already_checked_in'">
                <div class="bg-white border-[3.5px] border-red-600 rounded-2xl p-6 sm:p-7 shadow-[6px_6px_0_var(--ink)] space-y-6 animate-fade-in">
                    <div class="p-4 rounded-xl border-2 border-red-600 bg-red-50 shadow-[3px_3px_0_var(--ink)] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-600 text-white border-2 border-[var(--ink)] flex items-center justify-center font-black shadow-[2px_2px_0_var(--ink)] shrink-0 text-xl">
                                &excl;
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase text-red-600 tracking-wider">PERINGATAN GANDA (DOUBLE SCAN)</div>
                                <h3 class="font-heading font-black text-sm text-red-800" x-text="lastResult.message"></h3>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] shadow-[3px_3px_0_var(--ink)] space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">NOMOR BIB</div>
                                <div class="font-heading font-black text-2xl font-mono text-[var(--ink)]" x-text="lastResult.data.bib_number"></div>
                            </div>
                            <span class="chip font-bold bg-red-100 text-red-800 border border-[var(--ink)]" x-text="'Waktu: ' + lastResult.data.checked_in_at"></span>
                        </div>
                        <div>
                            <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">PESERTA TERDAFTAR</div>
                            <div class="font-heading font-black text-base text-[var(--ink)]" x-text="lastResult.data.participant_name"></div>
                        </div>
                        <p class="text-xs text-red-700 font-bold bg-red-100 p-2.5 rounded-lg border border-red-300">
                            Jangan serahkan paket racepack baru. Cek KTP asli peserta untuk verifikasi identitas pemilik sah tiket.
                        </p>
                    </div>

                    <button type="button" @click="resetResult()" class="btn btn-block btn-ghost font-black py-3 text-xs">
                        Tutup &amp; Pindai Tiket Lain
                    </button>
                </div>
            </template>

            {{-- 4. State Ditolak: Belum Lunas / Tidak Ditemukan / Batal --}}
            <template x-if="lastResult && ['not_found', 'unpaid', 'cancelled', 'network_error'].includes(lastResult.status)">
                <div class="bg-white border-[3.5px] border-[var(--ink)] rounded-2xl p-6 sm:p-7 shadow-[6px_6px_0_var(--ink)] space-y-6 animate-fade-in">
                    <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-red-100 shadow-[3px_3px_0_var(--ink)] flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-600 text-white border-2 border-[var(--ink)] flex items-center justify-center font-black text-xl shrink-0">
                            &times;
                        </div>
                        <div>
                            <div class="text-[10px] font-black uppercase text-red-700 tracking-wider">VERIFIKASI DITOLAK</div>
                            <h3 class="font-heading font-black text-sm text-[var(--ink)]" x-text="lastResult.message"></h3>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl border-2 border-dashed border-[var(--ink)] bg-[var(--paper)] text-xs font-semibold text-[var(--mute)]">
                        Pastikan peserta menunjukkan e-ticket resmi dari akun Velosha mereka dan berada pada event yang sesuai.
                    </div>

                    <button type="button" @click="resetResult()" class="btn btn-block btn-mustard font-black py-3 text-xs shadow-[2px_2px_0_var(--ink)]">
                        Coba Pindai Ulang
                    </button>
                </div>
            </template>

        </div>

    </main>

</div>

{{-- Library HTML5-QRCode via CDN Ringan --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endsection

