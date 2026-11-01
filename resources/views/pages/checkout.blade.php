@extends('layouts.app', ['title' => 'Formulir Pendaftaran & Checkout — Velosha'])

@section('content')
    @livewire('checkout', [
        'eventSlug' => (string) ($eventSlug ?? request()->query('event', '')),
        'categoryId' => $categoryId ?? (request()->filled('category') ? (int) request()->query('category') : null),
    ])
@endsection
