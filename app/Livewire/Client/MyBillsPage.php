<?php

namespace App\Livewire\Client;

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\PurchaseAccount;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

class MyBillsPage extends Component
{
    use WithFilePond, Actions, WithFileUploads;

    public $billingId;
    public $paymentMethod;
    public $referenceNo;
    public $proofOfPayment;

    public function getAccountsProperty()
    {
        return PurchaseAccount::with([
                'lot',
                'houseModel',
                'reservation',
                'billings' => fn ($query) => $query->orderBy('due_date'),
                'billings.latestPayment',
            ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }

    public function submitPayment()
    {
        $this->validate([
            'billingId' => 'required|exists:billings,id',
            'paymentMethod' => 'required|in:bank_transfer,gcash,maya,cash',
            'referenceNo' => 'nullable|string|max:255',
            'proofOfPayment' => 'required|file|max:20480',
        ]);

        DB::transaction(function () {
            $billing = Billing::with('purchaseAccount.billings')
                ->where('id', $this->billingId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->firstOrFail();

            abort_if($billing->purchaseAccount->user_id !== auth()->id(), 403);

            $firstPayableBilling = $billing->purchaseAccount
                ->billings()
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('due_date')
                ->first();

            abort_if(! $firstPayableBilling || $firstPayableBilling->id !== $billing->id, 403);

            $hasPendingPayment = $billing->payments()
                ->where('status', 'pending')
                ->exists();

            abort_if($hasPendingPayment, 403);

            $balance = $billing->amount_due - $billing->amount_paid;

            $path = $this->proofOfPayment->store(
                "billing-payments/{$billing->id}",
                'public'
            );

            BillingPayment::create([
                'billing_id' => $billing->id,
                'purchase_account_id' => $billing->purchase_account_id,
                'user_id' => auth()->id(),
                'amount' => $balance,
                'payment_method' => $this->paymentMethod,
                'reference_no' => $this->referenceNo,
                'proof_of_payment' => $path,
                'status' => 'pending',
                'paid_at' => now(),
            ]);
        });

        $this->reset([
            'billingId',
            'paymentMethod',
            'referenceNo',
            'proofOfPayment',
        ]);

        $this->dispatch('close-modal', name: 'payBill');

        $this->notification()->success(
            'Payment Submitted',
            'Your payment proof is now waiting for admin verification.'
        );
    }

    public function render()
    {
        return view('livewire.client.my-bills-page');
    }
}