<?php

namespace App\Livewire\Partials;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBadge extends Component
{
    public function getUnreadCountProperty()
    {
        // Only run this check if the user is authenticated
        if (!Auth::check()) {
            return 0;
        }

        return Auth::user()
            ->notifications()
            ->wherePivotNull('read_at')
            ->count();
    }
    
    public function render()
    {
        return view('livewire.partials.notification-badge');
    }
}
