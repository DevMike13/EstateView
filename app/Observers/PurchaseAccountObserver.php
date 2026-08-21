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
            'reservation.agent',
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
                'purchase_account_id' =>
                    $purchaseAccount->id,

                'client_name' =>
                    $purchaseAccount->user?->name,

                'client_email' =>
                    $purchaseAccount->user?->email,

                'agent_id' =>
                    $purchaseAccount->reservation?->agent_id,

                'agent_name' =>
                    $purchaseAccount->reservation?->agent?->name,

                'lot_name' =>
                    $purchaseAccount->lot?->name,

                'payment_scheme' =>
                    $purchaseAccount->payment_scheme,

                'remaining_balance' =>
                    $purchaseAccount->remaining_balance,

                'client_url' => route(
                    'client.bills',
                    [
                        'account' =>
                            $purchaseAccount->id,

                        'tab' =>
                            'unpaid',
                    ]
                ),
            ],
            'created_by' => auth()->id(),
        ]);

        $users = User::whereIn('role', ['admin', 'staff'])
            ->pluck('id')
            ->merge([
                $purchaseAccount->user_id,
            ])
            ->filter()
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
    // public function updated(PurchaseAccount $purchaseAccount): void
    // {
    //     if (! $purchaseAccount->wasChanged('status')) {
    //         return;
    //     }

    //     $purchaseAccount->loadMissing(['user', 'lot']);

    //     $message = "Ledger status for {$purchaseAccount->user?->name} was updated to "
    //         . str($purchaseAccount->status)->headline() . ".";

    //     $notification = Notification::create([
    //         'title' => 'Ledger Status Updated',
    //         'message' => $message,
    //         'type' => 'ledger_status_updated',
    //         'url' => route('filament.ev-admin.pages.client-ledgers'),
    //         'data' => [
    //             'purchase_account_id' => $purchaseAccount->id,
    //             'client_name' => $purchaseAccount->user?->name,
    //             'status' => $purchaseAccount->status,
    //         ],
    //         'created_by' => auth()->id(),
    //     ]);

    //     $users = User::whereIn('role', ['admin', 'staff'])
    //         ->pluck('id')
    //         ->merge([$purchaseAccount->user_id])
    //         ->unique()
    //         ->toArray();

    //     $notification->users()->attach($users);
    // }

    public function updated(PurchaseAccount $purchaseAccount): void
    {
        if (! auth()->check()) return;
        if (! in_array(auth()->user()->role, ['admin', 'staff'])) return;

        if (! $purchaseAccount->wasChanged([
            'status',
            'total_paid',
            'remaining_balance',
            'total_contract_price',
        ])) {
            return;
        }

        $purchaseAccount->loadMissing([
            'user',
            'lot',
            'houseModel',
            'reservation.agent',
        ]);

        $message = "Client ledger for {$purchaseAccount->user?->name} was updated by "
            . auth()->user()->name . ".";

        $notification = Notification::create([
            'title' => 'Client Ledger Updated',
            'message' => $message,
            'type' => 'client_ledger_updated',
            'url' => route('filament.ev-admin.pages.client-records'),
            'data' => [
                'purchase_account_id' => $purchaseAccount->id,
                'client_id' => $purchaseAccount->user_id,
                'client_name' => $purchaseAccount->user?->name,
                'agent_id' => $purchaseAccount->reservation?->agent_id,
                'agent_name' => $purchaseAccount->reservation?->agent?->name,
                'lot_name' => $purchaseAccount->lot?->name,
                'house_model' => $purchaseAccount->houseModel?->model_name,
                'status' => $purchaseAccount->status,
                'total_paid' => $purchaseAccount->total_paid,
                'remaining_balance' => $purchaseAccount->remaining_balance,
                'changes' => $purchaseAccount->getChanges(),
            ],
            'created_by' => auth()->id(),
        ]);

        $users = User::whereIn('role', ['admin', 'staff'])
            ->pluck('id')
            ->merge([
                $purchaseAccount->user_id,
            ])
            ->filter()
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
