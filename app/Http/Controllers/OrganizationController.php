<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function create(): View
    {
        return view('pages.organizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug !== '' ? $baseSlug : 'komunitas';
        $originalSlug = $slug;
        $counter = 1;

        while (Organization::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $organization = Organization::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        $organization->users()->attach($request->user()->id, [
            'role' => 'owner',
        ]);

        return redirect('/admin/events/create')
            ->with('status', 'Komunitas / Organisasi berhasil didaftarkan! Silakan buat event baru untuk komunitas Anda.');
    }
}
