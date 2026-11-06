@extends('layouts.app', ['title' => 'Ruang Tunggu Antrean Pendaftaran — Velosha'])

@section('content')
    @livewire('waiting-room', ['eventSlug' => $eventSlug, 'categoryId' => $categoryId])
@endsection

