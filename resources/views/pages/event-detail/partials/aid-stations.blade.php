{{-- Peta sirkuit + legenda pos hidrasi (aid stations) --}}
<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">Peta Sirkuit &amp; Pos Hidrasi</h2>
    </div>

    <div class="border border-veloborderstrong velo-box-1 overflow-hidden">
        @if($event->map_image_path)
            <div class="h-96 w-full bg-velosurfacealt">
                <img src="{{ asset('storage/' . ltrim($event->map_image_path, '/')) }}"
                     alt="Peta sirkuit {{ $event->title }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            </div>
        @else
            <div class="h-96 w-full bg-velosurfacealt flex flex-col items-center justify-center text-center px-6">
                <i data-lucide="map" class="w-10 h-10 text-[#9ca3af] mb-3"></i>
                <p class="text-sm font-bold text-teal-900">Peta rute belum tersedia</p>
                <p class="text-xs text-velomutedlight mt-1">Panitia belum mengunggah gambar peta sirkuit untuk event ini.</p>
            </div>
        @endif
    </div>
</div>
