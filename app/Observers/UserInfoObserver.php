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
    protected array $trackedFields = [
        'first_name'   => 'First Name',
        'middle_name'  => 'Middle Name',
        'last_name'    => 'Last Name',
        'suffix'       => 'Suffix',
        'phone'        => 'Phone Number',
        'region'       => 'Region',
        'province'     => 'Province',
        'municipality' => 'Municipality',
        'barangay'     => 'Barangay',
    ];

    public function updated(UserInfo $userInfo): void
    {
        if (! auth()->check()) return;

        $userInfo->loadMissing('user');

        $changes  = $userInfo->getChanges();
        $original = $userInfo->getOriginal();

        $diff = [];

        foreach ($this->trackedFields as $field => $label) {
            if (! array_key_exists($field, $changes)) continue;

            $old = $original[$field] ?? null;
            $new = $changes[$field] ?? null;

            if ($old === $new) continue;

            $diff[$field] = [
                'label' => $label,
                'old'   => $old,
                'new'   => $new,
            ];
        }

        if (empty($diff)) return;

        $notification = Notification::create([
            'title' => 'Client Personal Info Updated',
            'message' => "Personal information for {$userInfo->user?->name} was updated.",
            'type' => 'client_personal_info_updated',
            'url' => route('filament.ev-admin.pages.client-records'),
            'data' => [
                'client_id'   => $userInfo->user_id,
                'client_name' => $userInfo->user?->name,
                'changes'     => $diff,
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
