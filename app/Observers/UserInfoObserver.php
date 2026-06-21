<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserInfo;

class UserInfoObserver
{
    /**
     * Handle the UserInfo "created" event.
     */
    public function created(UserInfo $userInfo): void
    {
        //
    }

    /**
     * Handle the UserInfo "updated" event.
     */
    public function updated(UserInfo $userInfo): void
    {
        if (! auth()->check()) return;
        if (! in_array(auth()->user()->role, ['admin', 'staff'])) return;

        $userInfo->loadMissing('user');

        $notification = Notification::create([
            'title' => 'Client Personal Info Updated',
            'message' => "Personal information for {$userInfo->user?->name} was updated.",
            'type' => 'client_personal_info_updated',
            'url' => route('filament.ev-admin.pages.client-records'),
            'data' => [
                'client_id' => $userInfo->user_id,
                'client_name' => $userInfo->user?->name,
                'phone' => $userInfo->phone,
                'region' => $userInfo->region,
                'province' => $userInfo->province,
                'municipality' => $userInfo->municipality,
                'barangay' => $userInfo->barangay,
                'changes' => $userInfo->getChanges(),
            ],
            'created_by' => auth()->id(),
        ]);

        $staffAdmins = User::whereIn('role', ['admin', 'staff'])->pluck('id');

        $notification->users()->attach(
            $staffAdmins->merge([$userInfo->user_id])->unique()->toArray()
        );
    }

    /**
     * Handle the UserInfo "deleted" event.
     */
    public function deleted(UserInfo $userInfo): void
    {
        //
    }

    /**
     * Handle the UserInfo "restored" event.
     */
    public function restored(UserInfo $userInfo): void
    {
        //
    }

    /**
     * Handle the UserInfo "force deleted" event.
     */
    public function forceDeleted(UserInfo $userInfo): void
    {
        //
    }
}
