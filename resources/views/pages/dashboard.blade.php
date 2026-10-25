@extends('layouts.app', ['title' => 'Portal Rider — Velosha Dashboard', 'nav' => false])

@section('content')
<div class="min-h-screen bg-[var(--paper)]">
    @livewire('portal-dashboard')
</div>
@endsection
