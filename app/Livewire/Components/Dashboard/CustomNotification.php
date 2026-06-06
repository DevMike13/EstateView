<?php

namespace App\Livewire\Components\Dashboard;

use Livewire\Component;

class CustomNotification extends Component
{
    public $open = false;

    protected $listeners = [
        'notificationsRead' => '$refresh'
    ];

    public function getNotificationsProperty()
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->take(50)
            ->get();
    }

    public function markAsRead($notificationId)
    {
        auth()->user()
            ->notifications()
            ->updateExistingPivot($notificationId, [
                'read_at' => now()
            ]);

        $this->dispatch('notificationsRead');
    }

    public function markAllRead()
    {
        auth()->user()
            ->notifications()
            ->wherePivotNull('read_at')
            ->get()
            ->each(function ($notif) {
                auth()->user()
                    ->notifications()
                    ->updateExistingPivot($notif->id, [
                        'read_at' => now()
                    ]);
            });

        $this->dispatch('notificationsRead');
    }

    public function toggle()
    {
        $this->open = ! $this->open;
    }
    
    public function render()
    {
        return view('livewire.components.dashboard.custom-notification');
    }
}
