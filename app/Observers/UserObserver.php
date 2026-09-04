<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\User;

class UserObserver
{
    /**
     * Fields we want to track from the users table.
     */
    protected array $trackedFields = [
        'name'  => 'Full Name',
        'email' => 'Email Address',
    ];

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
        if (! auth()->check()) {
            return;
        }

        $actor = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | ONLY TRACK CLIENT / AGENT
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

        $changes = $user->getChanges();
        $original = $user->getOriginal();

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
        | BUILD FRIENDLY FIELD MESSAGE
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

            $changesText = $changedFields->implode(', ')
                . ', and '
                . $lastField;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / STAFF EDITED CLIENT / AGENT
        |--------------------------------------------------------------------------
        |
        | Send notification to the affected client/agent.
        |
        */

        if (in_array($actor->role, ['admin', 'staff'])) {

            $isAgent = $user->role === 'agent';

            $notification = Notification::create([
                'title' => 'Account Information Updated',

                'message' => "{$actor->name} has made changes to your {$changesText}.",

                'type' => $isAgent
                    ? 'agent_account_updated_by_staff'
                    : 'client_account_updated_by_staff',

                'url' => route('client.account'),

                'data' => [
                    'user_id'         => $user->id,
                    'user_name'       => $user->name,
                    'user_email'      => $user->email,
                    'role'            => $user->role,
                    'changes'         => $diff,
                    'updated_by'      => $actor->id,
                    'updated_by_name' => $actor->name,
                ],

                'created_by' => $actor->id,
            ]);

            $notification->users()->attach([
                $user->id,
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CLIENT / AGENT EDITED THEIR OWN ACCOUNT
        |--------------------------------------------------------------------------
        |
        | Send notification to admin + staff.
        |
        */

        if (in_array($actor->role, ['user', 'agent'])) {

            $isAgent = $user->role === 'agent';

            $notification = Notification::create([
                'title' => $isAgent
                    ? 'Agent Profile Updated'
                    : 'Client Account Updated',

                'message' => "{$user->name} has made changes to their {$changesText}.",

                'type' => $isAgent
                    ? 'agent_profile_updated'
                    : 'client_account_updated',

                'url' => $isAgent
                    ? route('filament.ev-admin.pages.agent-management')
                    : route('filament.ev-admin.pages.client-records'),

                'data' => [
                    'user_id'    => $user->id,
                    'user_name'  => $user->name,
                    'user_email' => $user->email,
                    'role'       => $user->role,
                    'changes'    => $diff,
                ],

                'created_by' => $actor->id,
            ]);

            $staffAdmins = User::whereIn('role', ['admin', 'staff'])
                ->where('id', '!=', $actor->id)
                ->pluck('id');

            $notification->users()->attach(
                $staffAdmins->toArray()
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (! auth()->check()) {
            return;
        }

        // Only Admin/Staff deletion actions.
        if (! in_array(auth()->user()->role, ['admin', 'staff'])) {
            return;
        }

        if (! in_array($user->role, ['user', 'agent'])) {
            return;
        }

        $isAgent = $user->role === 'agent';

        $title = $isAgent
            ? 'Agent Deleted'
            : 'Client Deleted';

        $message = $isAgent
            ? "Agent {$user->name} was deleted by " . auth()->user()->name . "."
            : "Client {$user->name} was deleted by " . auth()->user()->name . ".";

        $type = $isAgent
            ? 'agent_deleted'
            : 'client_deleted';

        $url = $isAgent
            ? route('filament.ev-admin.pages.agent-management')
            : route('filament.ev-admin.pages.client-records');

        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,

            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role' => $user->role,
            ],

            'created_by' => auth()->id(),
        ]);

        $staffAdmins = User::whereIn('role', ['admin', 'staff'])
            ->where('id', '!=', auth()->id())
            ->pluck('id');

        $notification->users()->attach(
            $staffAdmins->toArray()
        );
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