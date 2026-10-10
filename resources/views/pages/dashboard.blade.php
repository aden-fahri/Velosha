@extends('layouts.app', ['title' => 'Dashboard Peserta — Velosha'])

@section('content')
<div class="min-h-[calc(100vh-140px)] bg-velosurfacealt py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        @livewire('portal-dashboard')
    </div>
</div>
@endsection
