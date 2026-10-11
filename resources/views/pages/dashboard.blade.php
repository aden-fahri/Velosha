@extends('layouts.app', ['title' => 'Portal Rider — Velosha Dashboard', 'nav' => false])

@section('content')
<div class="min-h-screen bg-[#0d2a30]">
    @livewire('portal-dashboard')
</div>
@endsection
