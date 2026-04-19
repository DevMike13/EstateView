<?php

namespace App\Livewire;

use App\Models\Services;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('About')]
class AboutPage extends Component
{
    public function render()
    {
        return view('livewire.about-page');
    }
}
