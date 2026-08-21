<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if (! auth()->check()) return;
        if (! in_array(auth()->user()->role, ['admin', 'staff'])) return;
        if ($user->role !== 'user') return;

        if (! $user->wasChanged(['name', 'email'])) return;

        $this->notify(
            $user,
            'Client Account Updated',
            "Client account information for {$user->name} was updated.",
            'client_account_updated'
        );
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (! auth()->check()) return;
        if (! in_array(auth()->user()->role, ['admin', 'staff'])) return;
        if ($user->role !== 'user') return;

        $this->notify(
            $user,
            'Client Deleted',
            "Client {$user->name} was deleted by " . auth()->user()->name . ".",
            'client_deleted',
            includeSubject: false
        );
    }

    private function notify(User $user, string $title, string $message, string $type, bool $includeSubject = true): void
    {
        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => route('filament.ev-admin.pages.client-records'),
            'data' => [
                'client_id' => $user->id,
                'client_name' => $user->name,
                'client_email' => $user->email,
                'changes' => $user->getChanges(),
            ],
            'created_by' => auth()->id(),
        ]);

        $staffAdmins = User::whereIn('role', ['admin', 'staff'])->pluck('id');

        $recipients = $includeSubject
            ? $staffAdmins->merge([$user->id])
            : $staffAdmins;

        $notification->users()->attach($recipients->unique()->toArray());
    }
    
    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
