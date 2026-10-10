<?php

namespace App\Livewire;

use App\Models\Registration;
use App\Support\PanelScope;
use Livewire\Component;

class LiveRegistrations extends Component
{
    public function render()
    {
        return view('livewire.live-registrations', [
            'registrations' => PanelScope::registrations(Registration::query())
                ->with(['category.event'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
