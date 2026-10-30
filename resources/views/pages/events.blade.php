@extends('layouts.app', ['title' => 'Kalender Race — Velosha'])

@section('content')
<section class="pagehead bg-[#0A353E]">
    <div class="wrap" style="position: relative; z-index: 3">
        <div style="display: flex; gap: 8px; margin-bottom: 14px">
            <span class="chip p">Musim 2026/27</span>
            <span class="chip">{{ \App\Models\Event::published()->upcoming()->count() }} Event Terjadwal</span>
        </div>
        <h1 class="h2">Kalender race</h1>
        <p class="lead" style="margin: 16px 0 0; max-width: 520px; color: #10282E">
            Pilih jarak, pilih kota, lalu daftar. Semua event memakai timing chip RFID resmi dan medali finisher logam.
        </p>
    </div>
    <img class="band-art"
         src="/images/mockup/miring.png"
         alt="Pesepeda berjersey oranye melaju miring menikung">
</section>

<!-- Search, Filter & Katalog: in-place via Livewire -->
@livewire('event-catalog')
@endsection
