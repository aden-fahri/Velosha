@php($footer = \App\Models\FooterContent::current())
<footer class="relative overflow-hidden bg-teal-900 text-teal-100 border-t border-teal-800 mt-20">
    {{-- Kontur topo horizontal di atas latar teal gelap --}}
    <div class="absolute inset-0 pointer-events-none opacity-[0.07]"
         style="background-image:url('{{ asset('images/patterns/topo.svg') }}');background-size:520px auto;background-repeat:repeat"
         aria-hidden="true"></div>
    <div class="absolute inset-x-0 top-0 h-[3px] bg-race-500" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10">

            <!-- Brand -->
            <div class="col-span-2 md:col-span-1 space-y-4">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-14 w-auto object-contain invert opacity-95" />
                <p class="text-sm text-teal-100/70 leading-relaxed max-w-xs">
                    {{ $footer->brand_description }}
                </p>
            </div>

            <!-- Kategori -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-race-500 velo-box-1" aria-hidden="true"></span>
                    {{ $footer->kategori_title }}
                </h4>
                <ul class="space-y-2.5 text-sm">
                    @foreach (($footer->kategori_links ?? []) as $link)
                        <li><a href="{{ $link['url'] }}" wire:navigate class="text-teal-100/70 hover:text-race-400 transition">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Bantuan -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-race-500 velo-box-1" aria-hidden="true"></span>
                    {{ $footer->bantuan_title }}
                </h4>
                <ul class="space-y-2.5 text-sm">
                    @foreach (($footer->bantuan_links ?? []) as $link)
                        @unless (auth()->check() && (
                            ($link['url'] === '/dashboard' && (auth()->user()->isAdmin() || auth()->user()->isPenyelenggara()))
                            || ($link['url'] === '/admin' && auth()->user()->isPeserta())
                        ))
                        <li><a href="{{ $link['url'] }}" wire:navigate class="text-teal-100/70 hover:text-race-400 transition">{{ $link['label'] }}</a></li>
                        @endunless
                    @endforeach
                </ul>
            </div>

            <!-- Pembayaran -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-race-500 velo-box-1" aria-hidden="true"></span>
                    {{ $footer->payment_title }}
                </h4>
                <div class="grid grid-cols-2 gap-2 text-[11px] font-semibold">
                    @foreach (($footer->payment_methods ?? []) as $method)
                        <div class="border border-white/15 bg-white/5 text-teal-100/85 p-2 text-center velo-box-3">{{ $method['label'] }}</div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Bottom Subfooter -->
        <div class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-teal-100/60">
            <p>{{ $footer->copyright }}</p>
            <div class="flex gap-6">
                @foreach (($footer->bottom_links ?? []) as $link)
                    <a href="{{ $link['url'] }}" wire:navigate class="hover:text-race-400 transition">{{ $link['label'] }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
