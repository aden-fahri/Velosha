@extends('layouts.app', ['title' => 'Daftar Event & Race Finder — Velosha'])

@section('content')
<x-page-header
    eyebrow="Kalender Balap"
    title="Jelajahi Maraton Sepeda Indonesia"
    description="Temukan tantangan endurance berikutnya. Dari tanjakan vulkanik Jawa Timur hingga rute pesisir Bali dan Lombok."
    meta="{{ \App\Models\Event::published()->upcoming()->count() }} Event Terjadwal"
/>

<!-- Search, Filter & Katalog: in-place via Livewire -->
@livewire('event-catalog')
@endsection
