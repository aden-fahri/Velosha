@extends('layouts.app', ['title' => 'Formulir Pendaftaran & Checkout — Velosha'])

@section('content')
    @livewire('checkout', ['eventSlug' => $eventSlug, 'categoryId' => $categoryId])
@endsection
