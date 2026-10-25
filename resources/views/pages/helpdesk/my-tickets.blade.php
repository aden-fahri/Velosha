@extends('layouts.app', ['title' => 'Tiket Saya — Velosha Helpdesk', 'nav' => false])

@section('content')
    @livewire('helpdesk.my-tickets', ['ticket' => request()->route('ticket')])
@endsection
