<x-filament-widgets::widget class="fi-recent-registrations-widget">
    <style>
        .velo-rr-topbar {
            display: flex;
            justify-content: flex-end;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 0.5rem;
        }
        .dark .velo-rr-topbar { border-color: #374151; }

        .velo-rr-clock {
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            font-variant-numeric: tabular-nums;
        }
        .dark .velo-rr-clock { color: #9ca3af; }

        .velo-rr-grid {
            display: grid;
            grid-template-columns: 7rem minmax(0, 1fr) 7rem 7.5rem 5.5rem;
            gap: 0.75rem;
            align-items: center;
        }

        .velo-rr-head {
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #6b7280;
        }
        .dark .velo-rr-head { border-color: #374151; color: #9ca3af; }

        .velo-rr-row {
            padding: 0.625rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .velo-rr-row:last-of-type { border-bottom: 0; }
        .dark .velo-rr-row { border-color: #1f2937; }

        .velo-rr-bib {
            display: inline-block;
            background: #f0fdfa;
            color: #0f766e;
            padding: 0.125rem 0.375rem;
            border-radius: 1px;
            font-size: 0.6875rem;
            font-weight: 700;
        }
        .velo-rr-bib--empty {
            background: transparent;
            color: #9ca3af;
            padding: 0;
        }
        .dark .velo-rr-bib { background: #134e4a; color: #5eead4; }
        .dark .velo-rr-bib--empty { background: transparent; color: #6b7280; }

        .velo-rr-main { min-width: 0; }

        .velo-rr-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .velo-rr-name { color: #f9fafb; }

        .velo-rr-sub {
            font-size: 0.75rem;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .velo-rr-sub { color: #9ca3af; }

        .velo-rr-time {
            font-size: 0.75rem;
            color: #6b7280;
            font-variant-numeric: tabular-nums;
        }
        .dark .velo-rr-time { color: #9ca3af; }

        .velo-rr-aksi { text-align: right; }

        .velo-rr-empty {
            padding: 1.25rem 0;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }
        .dark .velo-rr-empty { color: #9ca3af; }

        @media (max-width: 767px) {
            .velo-rr-head { display: none; }
            .velo-rr-row { grid-template-columns: minmax(0, 1fr); gap: 0.375rem; }
            .velo-rr-aksi { text-align: left; }
        }
    </style>

    <x-filament::section heading="Pendaftar Terbaru">
        <div class="velo-rr-topbar">
            <span
                class="velo-rr-clock"
                x-data="{ jam: 'Memuat jam...' }"
                x-init="
                    const pad = (n) => String(n).padStart(2, '0');
                    const tick = () => {
                        const d = new Date();
                        const tanggal = d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
                        $data.jam = tanggal + ' \u00B7 ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
                    };
                    tick();
                    setInterval(tick, 1000);
                "
                x-text="jam"
            >Memuat jam...</span>
        </div>

        @livewire('live-registrations')
    </x-filament::section>
</x-filament-widgets::widget>
