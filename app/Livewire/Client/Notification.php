<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notifications')]
class Notification extends Component
{
    public function getNotificationsProperty()
    {
        return Auth::user()
            ->notifications()
            ->latest()
            ->get();
    }

    public function getUnreadCountProperty()
    {
        return Auth::user()
            ->notifications()
            ->wherePivotNull('read_at')
            ->count();
    }

    public function markAsRead($notificationId)
    {
        Auth::user()
            ->notifications()
            ->updateExistingPivot($notificationId, [
                'read_at' => now(),
            ]);
    }

    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->wherePivotNull('read_at')
            ->get()
            ->each(function ($notification) {
                Auth::user()
                    ->notifications()
                    ->updateExistingPivot($notification->id, [
                        'read_at' => now(),
                    ]);
            });
    }

    public function deleteNotification($notificationId)
    {
        Auth::user()
            ->notifications()
            ->detach($notificationId);
    }
    
    public function render()
    {
        return view('livewire.client.notification');
    }
}
