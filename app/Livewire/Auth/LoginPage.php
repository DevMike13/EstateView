<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Login')]

class LoginPage extends Component
{
    use Actions;
    
    public $email;
    public $password;

    public function login(){
        $this->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:255'
        ]);

        if(!auth()->attempt(['email' => $this->email, 'password' => $this->password])){
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Invalid credentials'
            );
            return;
        }

        if (auth()->user()->is_verified == 0) {
            auth()->logout(); 
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Your account is not verified. Please verify your account to log in.'
            );
            return;
        }

        if (
            auth()->user()->role === 'staff' &&
            auth()->user()->is_active == false
        ) {

            auth()->logout();

            $this->notification()->error(
                $title = 'Error!',
                $description = 'Your staff account is inactive. Please contact the administrator.'
            );

            return;
        }

        if (auth()->user()->role === 'admin') { 
            return redirect()->route('filament.ev-admin.pages.dashboard');
        }

        if (auth()->user()->role === 'staff') { 
            return redirect()->route('filament.ev-admin.pages.dashboard');
        }

        return redirect()->intended();
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
