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

    public function openNotification(int $notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('notifications.id', $notificationId)
            ->firstOrFail();

        auth()->user()
            ->notifications()
            ->updateExistingPivot($notification->id, [
                'read_at' => now(),
            ]);

        $data = $notification->data ?? [];

        /*
        |--------------------------------------------------------------------------
        | PAYMENT APPROVED
        |--------------------------------------------------------------------------
        |
        | Always send approved payments to Paid Bills.
        | This also fixes older notifications that were saved with tab=unpaid.
        |
        */
        if (
            $notification->type === 'billing_payment_approved' &&
            ! empty($data['purchase_account_id'])
        ) {
            return redirect()->to(
                route('client.bills', [
                    'account' => $data['purchase_account_id'],
                    'tab' => 'paid',
                    'highlight' => $data['billing_id'] ?? null,
                ])
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT REJECTED
        |--------------------------------------------------------------------------
        |
        | Rejected payments stay under Unpaid.
        |
        */
        if (
            $notification->type === 'billing_payment_rejected' &&
            ! empty($data['purchase_account_id'])
        ) {
            return redirect()->to(
                route('client.bills', [
                    'account' => $data['purchase_account_id'],
                    'tab' => 'unpaid',
                    'highlight' => $data['billing_id'] ?? null,
                ])
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CLIENT LEDGER CREATED
        |--------------------------------------------------------------------------
        */
        if (
            $notification->type === 'ledger_created' &&
            ! empty($data['purchase_account_id'])
        ) {
            return redirect()->to(
                route('client.bills', [
                    'account' => $data['purchase_account_id'],
                    'tab' => 'unpaid',
                ])
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT CLIENT URL
        |--------------------------------------------------------------------------
        */
        $clientUrl = $data['client_url'] ?? null;

        if (! $clientUrl) {
            return;
        }

        return redirect()->to($clientUrl);
    }
    
    public function render()
    {
        return view('livewire.client.notification');
    }
}
