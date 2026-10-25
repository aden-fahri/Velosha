<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Tiket Helpdesk — Velosha</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[var(--paper)] text-[var(--ink)] antialiased font-sans min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <header class="border-b-[3px] border-[var(--ink)] pb-4 mb-6 flex justify-between items-center">
            <div>
                <h1 class="font-heading font-black text-2xl">Helpdesk Operator Panel</h1>
                <p class="text-sm text-[var(--mute)]">Antrian tiket bantuan dan layanan keluhan Velosha.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-block px-3 py-1 bg-[var(--pine-l)] text-[var(--pine)] font-bold text-xs rounded border border-[var(--ink)]">
                    {{ auth()->user()->role }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-[var(--red)] underline font-bold">Keluar</button>
                </form>
            </div>
        </header>

        <main>
            <div class="bg-[var(--card)] border-[3px] border-[var(--ink)] rounded-xl p-6 shadow-[4px_4px_0_var(--ink)]">
                <h2 class="font-heading font-extrabold text-lg mb-2">Antrian Tiket (Placeholder Fase 1)</h2>
                <p class="text-sm text-[var(--ink)] leading-relaxed">
                    Sistem helpdesk sedang diinisialisasi. Komponen Livewire antrian tiket lengkap akan hadir pada fase berikutnya.
                </p>
            </div>
        </main>
    </div>
</body>
</html>
