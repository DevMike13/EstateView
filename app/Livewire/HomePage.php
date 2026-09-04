<?php

namespace App\Livewire;

use App\Models\HouseModel;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Home')]
class HomePage extends Component
{
    public function mount()
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        if (
            $user->role === 'admin'
            ||
            $user->role === 'staff'
        ) {
            return redirect()->route(
                'filament.ev-admin.pages.dashboard'
            );
        }

        if ($user->role === 'agent') {
            return redirect()->route(
                'agent.dashboard'
            );
        }
    }

    public function render()
    {
        $houseModels = HouseModel::latest()
            ->take(3)
            ->get();

        return view('livewire.home-page', [
            'houseModels' => $houseModels,
        ]);
    }
}