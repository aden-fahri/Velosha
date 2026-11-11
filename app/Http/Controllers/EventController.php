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
     * Detail event berdasarkan slug.
     * Penyelenggara, admin, dan operator dapat melihat pratinjau draf event mereka.
     */
    public function show(string $slug): Response
    {
        $query = Event::query()
            ->with(['categories', 'photos', 'organization'])
            ->where('slug', $slug);

        $user = auth()->user();
        $canPreview = $user && ($user->isAdmin() || $user->isOperator() || $user->isPenyelenggara());

        if (! $canPreview) {
            $query->published();
        }

        $event = $query->firstOrFail();

        return response()->view('pages.event-detail', compact('event'));
    }
}
