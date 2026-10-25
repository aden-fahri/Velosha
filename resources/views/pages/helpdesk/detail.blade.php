@extends('layouts.app', ['title' => 'Detail Tiket #' . $ticketId . ' — Velosha Helpdesk', 'nav' => false])

@section('content')
    @livewire('helpdesk.operator-ticket-detail', ['ticketId' => $ticketId])
@endsection
