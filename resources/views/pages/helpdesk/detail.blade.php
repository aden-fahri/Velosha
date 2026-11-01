@php
    $resolvedTicketId = $ticketId ?? request()->route('ticket');
@endphp
@extends('layouts.app', ['title' => 'Detail Tiket #' . $resolvedTicketId . ' — Velosha Helpdesk', 'nav' => false])

@section('content')
    @livewire('helpdesk.operator-ticket-detail', ['ticketId' => (int) $resolvedTicketId])
@endsection
