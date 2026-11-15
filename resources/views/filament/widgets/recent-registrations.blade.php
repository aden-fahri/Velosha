<x-filament-widgets::widget class="fi-recent-registrations-widget">
    <x-filament::section heading="Pendaftar Terbaru">
        @php
            $recentRegistrations = \App\Models\Registration::with(['category.event', 'user'])
                ->latest()
                ->limit(6)
                ->get();
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <th class="py-2.5 px-3">BIB / No</th>
                        <th class="py-2.5 px-3">Peserta</th>
                        <th class="py-2.5 px-3">Event & Kategori</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentRegistrations as $reg)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                            <td class="py-2.5 px-3 font-mono font-bold text-xs text-primary-600 dark:text-primary-400">
                                {{ $reg->bib_number ?: 'BELUM-BIB' }}
                            </td>
                            <td class="py-2.5 px-3 font-medium text-gray-900 dark:text-white">
                                {{ $reg->participant_name }}
                            </td>
                            <td class="py-2.5 px-3 text-gray-600 dark:text-gray-300">
                                {{ $reg->category?->event?->name ?? '-' }}
                                <span class="text-xs text-gray-400">({{ $reg->category?->name ?? '-' }})</span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $reg->status === 'paid' ? 'bg-[#D5EBD9] text-[#1F6F4A]' : 'bg-amber-100 text-amber-800' }}">
                                    {{ strtoupper($reg->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right text-xs text-gray-400">
                                {{ $reg->created_at?->diffForHumans() ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400 text-sm">
                                Belum ada pendaftar terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
