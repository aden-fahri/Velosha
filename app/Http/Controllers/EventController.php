<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Response;

class EventController extends Controller
{
    /**
     * Katalog balap (/events) — katalog dirender via Livewire EventCatalog.
     */
    public function index(): Response
    {
        return response()->view('pages.events');
    }

    /**
     * Detail event berdasarkan slug (hanya event published).
     */
    public function show(string $slug): Response
    {
        $event = Event::query()
            ->published()
            ->with(['categories', 'photos'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->view('pages.event-detail', compact('event'));
    }
}
