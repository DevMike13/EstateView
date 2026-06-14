<?php

namespace App\Observers;

use App\Mail\LedgerCreatedMail;
use App\Models\Notification;
use App\Models\PurchaseAccount;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class PurchaseAccountObserver
{
    /**
     * Handle the PurchaseAccount "created" event.
     */
    public function created(PurchaseAccount $purchaseAccount): void
    {
        $purchaseAccount->loadMissing([
            'user',
            'lot',
            'houseModel',
            'reservation',
        ]);

        $message = "A ledger account was created for {$purchaseAccount->user?->name}. "
            . "Payment scheme: " . str($purchaseAccount->payment_scheme)->headline()
            . ". Remaining balance: ₱" . number_format($purchaseAccount->remaining_balance, 2) . ".";

        $notification = Notification::create([
            'title' => 'Client Ledger Created',
            'message' => $message,
            'type' => 'ledger_created',
            'url' => route('filament.ev-admin.pages.client-ledgers'),
            'data' => [
                'purchase_account_id' => $purchaseAccount->id,
                'client_name' => $purchaseAccount->user?->name,
                'client_email' => $purchaseAccount->user?->email,
                'lot_name' => $purchaseAccount->lot?->name,
                'payment_scheme' => $purchaseAccount->payment_scheme,
                'remaining_balance' => $purchaseAccount->remaining_balance,
            ],
            'created_by' => auth()->id(),
        ]);

        $users = User::whereIn('role', ['admin', 'staff'])
            ->pluck('id')
            ->merge([$purchaseAccount->user_id])
            ->unique()
            ->toArray();

        $notification->users()->attach($users);

        if ($purchaseAccount->user?->email) {
            Mail::to($purchaseAccount->user->email)
                ->send(new LedgerCreatedMail($purchaseAccount));
        }
    }

    /**
     * Handle the PurchaseAccount "updated" event.
     */
    public function updated(PurchaseAccount $purchaseAccount): void
    {
        if (! $purchaseAccount->wasChanged('status')) {
            return;
        }

        $purchaseAccount->loadMissing(['user', 'lot']);

        $message = "Ledger status for {$purchaseAccount->user?->name} was updated to "
            . str($purchaseAccount->status)->headline() . ".";

        $notification = Notification::create([
            'title' => 'Ledger Status Updated',
            'message' => $message,
            'type' => 'ledger_status_updated',
            'url' => route('filament.ev-admin.pages.client-ledgers'),
            'data' => [
                'purchase_account_id' => $purchaseAccount->id,
                'client_name' => $purchaseAccount->user?->name,
                'status' => $purchaseAccount->status,
            ],
            'created_by' => auth()->id(),
        ]);

        $users = User::whereIn('role', ['admin', 'staff'])
            ->pluck('id')
            ->merge([$purchaseAccount->user_id])
            ->unique()
            ->toArray();

        $notification->users()->attach($users);
    }

    /**
     * Handle the PurchaseAccount "deleted" event.
     */
    public function deleted(PurchaseAccount $purchaseAccount): void
    {
        //
    }

    /**
     * Handle the PurchaseAccount "restored" event.
     */
    public function restored(PurchaseAccount $purchaseAccount): void
    {
        //
    }

    /**
     * Handle the PurchaseAccount "force deleted" event.
     */
    public function forceDeleted(PurchaseAccount $purchaseAccount): void
    {
        //
    }
}
