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

    public function login()
    {
        $this->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:255'
        ]);

        if (!auth()->attempt([
            'email' => $this->email,
            'password' => $this->password
        ])) {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Invalid credentials'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK IF ACCOUNT IS VERIFIED
        |--------------------------------------------------------------------------
        */

        if (auth()->user()->is_verified == 0) {
            auth()->logout();

            $this->notification()->error(
                $title = 'Error!',
                $description = 'Your account is not verified. Please verify your account to log in.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK IF ACCOUNT IS ACTIVE
        |--------------------------------------------------------------------------
        */

        if (!auth()->user()->is_active) {
            auth()->logout();

            $this->notification()->error(
                $title = 'Account Inactive',
                $description = 'Your account is inactive. Please contact the administrator.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        request()->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE ROLE
        |--------------------------------------------------------------------------
        */

        $role = strtolower(
            trim(
                auth()->user()->role
            )
        );


        /*
        |--------------------------------------------------------------------------
        | REDIRECT ADMIN / STAFF
        |--------------------------------------------------------------------------
        */

        if (
            $role === 'admin'
            ||
            $role === 'staff'
        ) {
            return redirect()->route(
                'filament.ev-admin.pages.dashboard'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT AGENT
        |--------------------------------------------------------------------------
        */

        if ($role === 'agent') {
            return redirect()->route(
                'agent.dashboard'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CLIENT / USER
        |--------------------------------------------------------------------------
        */

        return redirect()->route(
            'user.home'
        );
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }
}