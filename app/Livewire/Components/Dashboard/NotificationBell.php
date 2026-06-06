<?php

namespace App\Livewire\Components\Dashboard;

use Livewire\Component;

class NotificationBell extends Component
{
    protected $listeners = ['notificationsRead' => '$refresh'];

    public function getCountProperty()
    {
        return auth()->user()
            ->notifications()
            ->wherePivotNull('read_at')
            ->count();
    }
    
    public function render()
    {
        return view('livewire.components.dashboard.notification-bell');
    }
}
