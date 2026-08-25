<?php

namespace App\Observers;

use App\Mail\BillingPaymentApprovedMail;
use App\Mail\BillingPaymentRejectedMail;
use App\Mail\BillingPaymentSubmittedMail;
use App\Mail\OfficeBillingPaymentRecordedMail;
use App\Models\BillingPayment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class BillingPaymentObserver
{
    /**
     * Handle the BillingPayment "created" event.
     */
    public function created(BillingPayment $billingPayment): void
    {
        $billingPayment->loadMissing([
            'user',
            'billing',
            'purchaseAccount.user',
            'purchaseAccount.lot',
            'purchaseAccount.reservation.agent',
        ]);

        if ($billingPayment->source === 'client_upload') {
            $this->notifyAdmins(
                $billingPayment,
                'Client Payment Submitted',
                "{$billingPayment->user?->name} submitted a payment proof for {$billingPayment->billing?->title}.",
                'billing_payment_submitted'
            );

            $admins = User::whereIn('role', ['admin', 'staff'])->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->send(new BillingPaymentSubmittedMail($billingPayment));
            }
        }

        if ($billingPayment->source === 'office_payment') {

            $billingPayment->loadMissing([
                'billing',
                'purchaseAccount.user',
                'verifier',
            ]);

            $recordedBy = $billingPayment->verifier?->name
                ?? auth()->user()?->name
                ?? 'Admin / Staff';

            $this->notifyClientAndAdmins(
                $billingPayment,
                'Office Payment Recorded',
                "{$recordedBy} recorded an office payment for {$billingPayment->billing?->title}.",
                'office_payment_recorded'
            );

            if ($billingPayment->purchaseAccount?->user?->email) {
                Mail::to(
                    $billingPayment->purchaseAccount->user->email
                )->send(
                    new OfficeBillingPaymentRecordedMail($billingPayment)
                );
            }
        }
    }

    /**
     * Handle the BillingPayment "updated" event.
     */
    public function updated(BillingPayment $billingPayment): void
    {
        if (! $billingPayment->wasChanged('status')) {
            return;
        }

        $billingPayment->loadMissing([
            'user',
            'billing',
            'purchaseAccount.user',
            'purchaseAccount.reservation.agent',
        ]);

        $performedBy = auth()->check()
            ? (
                auth()->user()->role === 'staff'
                    ? auth()->user()->name
                    : 'Admin'
            )
            : 'System';

        if ($billingPayment->status === 'verified') {
            $this->notifyClientAndAdmins(
                $billingPayment,
                'Payment Approved',
                "Payment for {$billingPayment->billing?->title} was approved. Updated by: {$performedBy}.",
                'billing_payment_approved'
            );

            Mail::to($billingPayment->purchaseAccount->user->email)
                ->send(new BillingPaymentApprovedMail($billingPayment));
        }

        if ($billingPayment->status === 'rejected') {
            $this->notifyClientAndAdmins(
                $billingPayment,
                'Payment Rejected',
                "Payment for {$billingPayment->billing?->title} was rejected. Updated by: {$performedBy}.",
                'billing_payment_rejected'
            );

            Mail::to($billingPayment->purchaseAccount->user->email)
                ->send(new BillingPaymentRejectedMail($billingPayment));
        }
    }

    private function notifyAdmins(
        BillingPayment $payment,
        string $title,
        string $message,
        string $type
    ): void {
        
        $payment->loadMissing([
            'purchaseAccount.user',
            'purchaseAccount.reservation.agent',
        ]);

        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => route('filament.ev-admin.pages.client-ledgers'),
            'data' => [
                'billing_payment_id' => $payment->id,
                'billing_id' => $payment->billing_id,
                'purchase_account_id' => $payment->purchase_account_id,
                'client_name' => $payment->purchaseAccount?->user?->name,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'source' => $payment->source,
                'agent_id' => $payment->purchaseAccount?->reservation?->agent_id,
                'agent_name' => $payment->purchaseAccount?->reservation?->agent?->name,
            ],
            'created_by' => auth()->id(),
        ]);

        $users = User::whereIn('role', ['admin', 'staff'])
            ->pluck('id')
            ->filter()
            ->unique()
            ->toArray();

        $notification->users()->attach($users);
    }

    private function notifyClientAndAdmins(
        BillingPayment $payment,
        string $title,
        string $message,
        string $type
    ): void {

        $payment->loadMissing([
            'billing',
            'purchaseAccount.user',
            'purchaseAccount.reservation.agent',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CLIENT BILL TAB
        |--------------------------------------------------------------------------
        |
        | Verified payment = Paid tab
        | Rejected / other payment = Unpaid tab
        |
        */
        $billingTab = $payment->status === 'verified'
            ? 'paid'
            : 'unpaid';

        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,

            'url' => route(
                'filament.ev-admin.pages.client-ledgers'
            ),

            'data' => [
                'billing_payment_id' =>
                    $payment->id,

                'billing_id' =>
                    $payment->billing_id,

                'purchase_account_id' =>
                    $payment->purchase_account_id,

                'client_name' =>
                    $payment->purchaseAccount?->user?->name,

                'amount' =>
                    $payment->amount,

                'status' =>
                    $payment->status,

                'source' =>
                    $payment->source,

                'agent_id' =>
                    $payment
                        ->purchaseAccount
                        ?->reservation
                        ?->agent_id,

                'agent_name' =>
                    $payment
                        ->purchaseAccount
                        ?->reservation
                        ?->agent
                        ?->name,

                'client_url' => route(
                    'client.bills',
                    [
                        'account' =>
                            $payment->purchase_account_id,

                        'tab' =>
                            $billingTab,

                        'highlight' =>
                            $payment->billing_id,
                    ]
                ),
            ],

            'created_by' => auth()->id(),
        ]);

        $users = User::whereIn(
            'role',
            ['admin', 'staff']
        )
            ->pluck('id')
            ->merge([
                $payment
                    ->purchaseAccount
                    ?->user_id,
            ])
            ->filter()
            ->unique()
            ->toArray();

        $notification
            ->users()
            ->attach($users);
    }

    /**
     * Handle the BillingPayment "deleted" event.
     */
    public function deleted(BillingPayment $billingPayment): void
    {
        //
    }

    /**
     * Handle the BillingPayment "restored" event.
     */
    public function restored(BillingPayment $billingPayment): void
    {
        //
    }

    /**
     * Handle the BillingPayment "force deleted" event.
     */
    public function forceDeleted(BillingPayment $billingPayment): void
    {
        //
    }
}
