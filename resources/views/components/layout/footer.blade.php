@php($footer = \App\Models\FooterContent::current())
<footer class="relative bg-[var(--ink)] text-[var(--paper)] border-t-[3px] border-[var(--ink)] mt-20">
    {{-- Halftone strip dekoratif di bagian paling atas footer --}}
    <div class="h-3 w-full bg-[var(--orange)] border-b-[3px] border-[var(--ink)]" aria-hidden="true"></div>

    <div class="wrap py-14">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10">

            <!-- Brand -->
            <div class="col-span-2 md:col-span-1 space-y-4">
                <a href="/" wire:navigate class="inline-block">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-12 w-auto object-contain brightness-0 invert opacity-95" />
                </a>
                <p class="text-sm text-[var(--paper2)]/80 leading-relaxed max-w-xs">
                    {{ $footer->brand_description }}
                </p>
            </div>

            <!-- Kategori -->
            <div>
                <h3 class="font-heading font-extrabold text-[15px] uppercase tracking-wider text-[var(--mustard)] mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-[var(--orange)] rotate-45 border border-[var(--paper)]" aria-hidden="true"></span>
                    {{ $footer->kategori_title }}
                </h3>
                <ul class="space-y-2.5 text-sm">
                    @foreach (($footer->kategori_links ?? []) as $link)
                        <li>
                            <a href="{{ $link['url'] }}" wire:navigate class="text-[var(--paper)]/80 hover:text-[var(--orange-l)] transition-colors">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Bantuan -->
            <div>
                <h3 class="font-heading font-extrabold text-[15px] uppercase tracking-wider text-[var(--mustard)] mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-[var(--orange)] rotate-45 border border-[var(--paper)]" aria-hidden="true"></span>
                    {{ $footer->bantuan_title }}
                </h3>
                <ul class="space-y-2.5 text-sm">
                    @foreach (($footer->bantuan_links ?? []) as $link)
                        @unless (auth()->check() && (
                            ($link['url'] === '/dashboard' && (auth()->user()->isAdmin() || auth()->user()->isPenyelenggara()))
                            || ($link['url'] === '/admin' && auth()->user()->isPeserta())
                        ))
                        <li>
                            <a href="{{ $link['url'] }}" wire:navigate class="text-[var(--paper)]/80 hover:text-[var(--orange-l)] transition-colors">
                                {{ $link['label'] }}
                            </a>
                        </li>
                        @endunless
                    @endforeach
                </ul>
            </div>

            <!-- Pembayaran -->
            <div>
                <h3 class="font-heading font-extrabold text-[15px] uppercase tracking-wider text-[var(--mustard)] mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-[var(--orange)] rotate-45 border border-[var(--paper)]" aria-hidden="true"></span>
                    {{ $footer->payment_title }}
                </h3>
                <div class="grid grid-cols-2 gap-2 text-[12px] font-bold">
                    @foreach (($footer->payment_methods ?? []) as $method)
                        <div class="border-[2px] border-[var(--line)] bg-[var(--paper)]/10 text-[var(--paper)] p-2.5 text-center rounded-lg">
                            {{ $method['label'] }}
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Bottom Subfooter -->
        <div class="mt-12 pt-6 border-t-[2px] border-[var(--paper)]/20 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-[var(--paper)]/60">
            <p>{{ $footer->copyright }}</p>
            <div class="flex gap-6">
                @foreach (($footer->bottom_links ?? []) as $link)
                    <a href="{{ $link['url'] }}" wire:navigate class="hover:text-[var(--orange-l)] transition-colors">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
