@extends('layouts.app', ['title' => 'Ranking & Leaderboard — Velosha'])

@section('content')

<x-page-header
    eyebrow="Hasil Balapan"
    title="Ranking & Leaderboard"
    description="Hasil resmi dari setiap event, dicatat dengan timing chip RFID dan diverifikasi oleh Race Comisaris sebelum dipublikasikan."
    meta="Diperbarui · 30 September 2026"
/>

<!-- Filter Event -->
<section class="py-8 bg-white border-b border-veloborder sticky top-20 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 overflow-x-auto">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#5f6a70] shrink-0">Pilih Event</span>
            <div class="flex items-center gap-2">
                @foreach(['Gran Fondo Borobudur 130KM', 'Bromo Summit 95KM', 'Bali Sunrise 75KM'] as $e)
                <span class="px-4 py-2 text-xs font-medium border border-veloborderstrong velo-box-1 whitespace-nowrap transition-colors {{ $loop->first ? 'bg-race-300 text-race-900 font-bold' : 'bg-white text-teal-700' }}">{{ $e }}</span>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Podium -->
<section class="py-16 bg-velosurfacealt border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <div class="w-10 h-1 bg-race-500 velo-box-1 mb-3"></div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight">Podium 130KM</h2>
            </div>
            <div class="text-xs text-[#556066] font-semibold">10 Juni 2026 · Selesai</div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['2', 'Bagas Wicaksono', '03:01:12', '32 km/h', 'bg-velosurface', 'text-teal-900'],
                ['1', 'Rizky Pratama', '02:58:44', '43 km/h', 'bg-teal-700', 'text-white'],
                ['3', 'Andi Saputra', '03:04:57', '22 km/h', 'bg-velosurface', 'text-teal-900'],
            ] as $p)
            <div class="border velo-box-1 p-6 flex flex-col items-center text-center {{ $p[5] === 'text-white' ? 'bg-teal-700 border-teal-800' : 'bg-white border-veloborder' }}">
                <div class="w-12 h-12 {{ $p[5] === 'text-white' ? 'bg-race-400 text-[#2E343B]' : 'bg-race-500 text-white' }} text-sm font-black flex items-center justify-center velo-box-1 mb-4">{{ $p[0] }}</div>
                <div class="text-sm font-extrabold mb-1">{{ $p[1] }}</div>
                <div class="font-mono text-xl font-black {{ $p[5] === 'text-white' ? 'text-race-400' : 'text-race-700' }}">{{ $p[2] }}</div>
                <div class="text-[11px] {{ $p[5] === 'text-white' ? 'text-teal-100' : 'text-[#556066]' }} mt-2">Rata-rata {{ $p[3] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Leaderboard -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="w-10 h-1 bg-teal-500 velo-box-1 mb-3"></div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mb-8">Leaderboard Lengkap</h2>

        <div class="border border-veloborder velo-box-1 overflow-hidden">
            <div class="bg-velosurface px-5 py-3 border-b border-veloborder flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-teal-700">
                <span>Gran Fondo Borobudur 130KM</span>
                <span class="font-mono text-[#556066] normal-case">842 finishers</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[560px]">
                    <thead class="bg-white border-b border-veloborder">
                        <tr>
                            @foreach(['Pos', 'Rider', 'Kategori', 'Waktu', 'Avg Speed'] as $th)
                            <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-[#5f6a70]">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-velosurface">
                        @foreach([
                            [1, 'Rizky Pratama', 'Gran Fondo', '02:58:44', '43.7 km/h', 'bg-race-500 text-white'],
                            [2, 'Bagas Wicaksono', 'Gran Fondo', '03:01:12', '43.1 km/h', 'bg-velosurfacealt text-teal-800'],
                            [3, 'Andi Saputra', 'Gran Fondo', '03:04:57', '42.5 km/h', 'bg-velosurfacealt text-teal-800'],
                            [4, 'Dimas Anggara', 'Gran Fondo', '03:09:33', '41.9 km/h', 'bg-velosurfacealt text-teal-800'],
                            [5, 'Fajar Ramadhan', 'Gran Fondo', '03:12:08', '41.6 km/h', 'bg-velosurfacealt text-teal-800'],
                            [6, 'Yoga Mahendra', 'Medio Fondo', '03:18:42', '40.1 km/h', ''],
                            [7, 'Hendra Kusuma', 'Gran Fondo', '03:21:55', '39.8 km/h', ''],
                            [8, 'Bayu Pamungkas', 'KOM Climb', '03:24:10', '39.5 km/h', ''],
                            [9, 'Arif Setiawan', 'Medio Fondo', '03:26:37', '39.2 km/h', ''],
                            [10, 'Rizky Pratama', 'Gran Fondo', '03:29:04', '38.9 km/h', ''],
                        ] as $row)
                        <tr class="hover:bg-velosurfacealt transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="w-7 h-7 {{ $row[5] ?: 'bg-white border border-veloborder text-teal-800' }} text-xs font-bold flex items-center justify-center velo-box-1">{{ $row[0] }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-xs font-bold text-teal-900">{{ $row[1] }}</td>
                            <td class="px-5 py-3.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 border border-veloborder px-2 py-0.5 velo-box-1">{{ $row[2] }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-race-700">{{ $row[3] }}</td>
                            <td class="px-5 py-3.5 text-xs text-[#556066]">{{ $row[4] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-[11px] text-[#5f6a70] mt-6 leading-relaxed">
            Hasil bersifat provisional sampai Race Comisaris menyelesaikan verifikasi. Koreksi waktu dapat dilakukan
            maksimal 7 hari setelah event. Kecepatan rata-rata dihitung dari jarak resmi dibagi waktu tempuh.
        </p>
    </div>
</section>

<!-- CTA -->
<section class="py-14 bg-velosurfacealt border-t border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-5">
        <div>
            <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">Ingin masuk leaderboard?</h2>
            <p class="text-xs text-[#556066] mt-1">Daftar event berikutnya sebelum kuota kelas habis.</p>
        </div>
        <a href="/events" class="inline-flex items-center justify-center bg-race-500 hover:bg-race-600 text-white font-semibold px-6 py-3.5 text-xs tracking-wider uppercase velo-box-1 transition shrink-0">
            <span>Lihat Semua Race</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
        </a>
    </div>
</section>

@endsection
