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
        if (! auth()->check()) {
            return;
        }

        $actor = auth()->user();
        $user = $userInfo->user;

        if (! $user) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ONLY CLIENT / AGENT PROFILE RECORDS
        |--------------------------------------------------------------------------
        */

        if (! in_array($user->role, ['user', 'agent'])) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD CHANGES
        |--------------------------------------------------------------------------
        */

        $changes = $userInfo->getChanges();
        $original = $userInfo->getOriginal();

        $diff = [];

        foreach ($this->trackedFields as $field => $label) {

            if (! array_key_exists($field, $changes)) {
                continue;
            }

            $old = $original[$field] ?? null;
            $new = $changes[$field] ?? null;

            if ($old === $new) {
                continue;
            }

            $diff[$field] = [
                'label' => $label,
                'old'   => $old,
                'new'   => $new,
            ];
        }

        if (empty($diff)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD CHANGED FIELD TEXT
        |--------------------------------------------------------------------------
        */

        $changedFields = collect($diff)
            ->pluck('label')
            ->map(fn ($label) => strtolower($label))
            ->values();

        if ($changedFields->count() === 1) {

            $changesText = $changedFields->first();

        } elseif ($changedFields->count() === 2) {

            $changesText = $changedFields->implode(' and ');

        } else {

            $lastField = $changedFields->pop();

            $changesText =
                $changedFields->implode(', ')
                . ', and '
                . $lastField;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / STAFF EDITED CLIENT OR AGENT
        |--------------------------------------------------------------------------
        |
        | Send the notification TO the affected user.
        |
        */

        if (in_array($actor->role, ['admin', 'staff'])) {

            $isAgent = $user->role === 'agent';

            $notification = Notification::create([
                'title' => $isAgent
                    ? 'Account Information Updated'
                    : 'Personal Information Updated',

                'message' =>
                    "{$actor->name} has made changes to your {$changesText}.",

                'type' => $isAgent
                    ? 'agent_account_updated_by_staff'
                    : 'client_account_updated_by_staff',

                'url' => route('client.account'),

                'data' => [
                    'user_id'         => $user->id,
                    'user_name'       => $user->name,
                    'role'            => $user->role,
                    'changes'         => $diff,
                    'updated_by'      => $actor->id,
                    'updated_by_name' => $actor->name,
                ],

                'created_by' => $actor->id,
            ]);

            /*
            * Only the affected client / agent receives it.
            */
            $notification->users()->attach([
                $user->id,
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CLIENT / AGENT EDITED THEIR OWN INFORMATION
        |--------------------------------------------------------------------------
        |
        | Send the notification TO admin + staff.
        |
        */

        if (in_array($actor->role, ['user', 'agent'])) {

            $isAgent = $user->role === 'agent';

            $notification = Notification::create([
                'title' => $isAgent
                    ? 'Agent Profile Updated'
                    : 'Client Personal Info Updated',

                'message' =>
                    "{$user->name} has made changes to their {$changesText}.",

                'type' => $isAgent
                    ? 'agent_profile_updated'
                    : 'client_personal_info_updated',

                'url' => $isAgent
                    ? route('filament.ev-admin.pages.agent-management')
                    : route('filament.ev-admin.pages.client-records'),

                'data' => [
                    'user_id'   => $user->id,
                    'user_name' => $user->name,
                    'role'      => $user->role,
                    'changes'   => $diff,
                ],

                'created_by' => $actor->id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | SEND TO ADMIN + STAFF
            |--------------------------------------------------------------------------
            */

            $staffAdmins = User::whereIn(
                'role',
                ['admin', 'staff']
            )
                ->where('id', '!=', $actor->id)
                ->pluck('id');

            $notification->users()->attach(
                $staffAdmins->toArray()
            );
        }
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
