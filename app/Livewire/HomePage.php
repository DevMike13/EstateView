<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Home')]
class HomePage extends Component
{
    public function mount()
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'staff'])) {
            return redirect()->route('filament.ev-admin.pages.dashboard');
        }
    }
    public function render()
    {
        return view('livewire.home-page');
    }
}
