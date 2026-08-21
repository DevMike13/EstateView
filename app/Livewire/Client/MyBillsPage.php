<?php

namespace App\Livewire\Client;

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\PaymentQrCode;
use App\Models\PurchaseAccount;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

#[Title('My Bills')]
class MyBillsPage extends Component
{
    use WithFilePond, Actions, WithFileUploads;

    public $billingId;
    public $paymentMethod;
    public $referenceNo;
    public $proofOfPayment;

    public ?int $highlight = null;
    public ?int $highlightAccount = null;

    public string $activeBillingTab = 'unpaid';

    public function mount(): void
    {
        $this->highlight = request()->query('highlight')
            ? (int) request()->query('highlight')
            : null;

        $this->highlightAccount = request()->query('account')
            ? (int) request()->query('account')
            : null;

        $requestedTab = request()->query(
            'tab',
            'unpaid'
        );

        $this->activeBillingTab = in_array(
            $requestedTab,
            ['unpaid', 'paid'],
            true
        )
            ? $requestedTab
            : 'unpaid';
    }

    /*
    |--------------------------------------------------------------------------
    | Accounts
    |--------------------------------------------------------------------------
    */

    public function getAccountsProperty()
    {
        return PurchaseAccount::query()
            ->with([
                'lot',
                'houseModel',
                'reservation',

                'billings' => fn ($query) =>
                    $query->orderBy('due_date'),

                'billings.latestPayment',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Currently Selected Billing
    |--------------------------------------------------------------------------
    */

    public function getSelectedBillingProperty()
    {
        if (! $this->billingId) {
            return null;
        }

        return Billing::query()
            ->with([
                'purchaseAccount',
                'latestPayment',
            ])
            ->whereKey($this->billingId)
            ->whereHas(
                'purchaseAccount',
                fn ($query) =>
                    $query->where(
                        'user_id',
                        auth()->id()
                    )
            )
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Submit Client Payment
    |--------------------------------------------------------------------------
    */

    public function submitPayment(): void
    {
        $this->validate([
            'billingId' => [
                'required',
                'exists:billings,id',
            ],

            'paymentMethod' => [
                'required',
                'in:bank_transfer,gcash,maya,cash',
            ],

            'referenceNo' => [
                'required',
                'string',
                'max:255',
            ],

            'proofOfPayment' => [
                'required',
                'file',
                'max:20480',
            ],
        ]);

        DB::transaction(function () {

            /*
             * Lock the billing while creating the payment.
             *
             * This helps prevent duplicate payment submissions
             * from fast repeated requests.
             */
            $billing = Billing::query()
                ->with([
                    'purchaseAccount.billings',
                    'payments',
                ])
                ->whereKey($this->billingId)
                ->whereIn(
                    'status',
                    ['unpaid', 'partial']
                )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Client must own this billing.
             */
            abort_if(
                $billing->purchaseAccount->user_id
                    !== auth()->id(),
                403
            );

            /*
             * Only the oldest unpaid/partial billing
             * may be paid.
             */
            $firstPayableBilling =
                $billing
                    ->purchaseAccount
                    ->billings()
                    ->whereIn(
                        'status',
                        ['unpaid', 'partial']
                    )
                    ->orderBy('due_date')
                    ->orderBy('id')
                    ->first();

            abort_if(
                ! $firstPayableBilling
                || $firstPayableBilling->id
                    !== $billing->id,
                403
            );

            /*
             * Prevent another payment submission while an
             * existing one is still waiting for verification.
             */
            $hasPendingPayment =
                $billing
                    ->payments()
                    ->where(
                        'status',
                        'pending'
                    )
                    ->exists();

            abort_if(
                $hasPendingPayment,
                403
            );

            /*
             * IMPORTANT:
             * Snapshot the billing adjustment NOW.
             *
             * The admin may verify this tomorrow,
             * therefore we should not recalculate using
             * tomorrow's date.
             */
            $baseAmount =
                (float) $billing->remaining_balance;

            $discountAmount =
                (float) $billing->calculated_discount;

            $penaltyAmount =
                (float) $billing->calculated_penalty;

            $penaltyMonths =
                (int) $billing->months_overdue;

            $payableAmount =
                (float) $billing->payable_amount;

            /*
             * Save proof.
             */
            $path = $this
                ->proofOfPayment
                ->store(
                    "billing-payments/{$billing->id}",
                    'public'
                );

            BillingPayment::create([
                'billing_id' =>
                    $billing->id,

                'purchase_account_id' =>
                    $billing->purchase_account_id,

                'user_id' =>
                    auth()->id(),

                /*
                 * Snapshot values
                 */
                'base_amount' =>
                    $baseAmount,

                'discount_amount' =>
                    $discountAmount,

                'penalty_amount' =>
                    $penaltyAmount,

                'penalty_months' =>
                    $penaltyMonths,

                /*
                 * Actual requested payment amount.
                 */
                'amount' =>
                    $payableAmount,

                'payment_method' =>
                    $this->paymentMethod,

                'reference_no' =>
                    $this->referenceNo,

                'proof_of_payment' =>
                    $path,

                'status' =>
                    'pending',

                'source' =>
                    'client_upload',

                'paid_at' =>
                    now(),
            ]);
        });

        $this->reset([
            'billingId',
            'paymentMethod',
            'referenceNo',
            'proofOfPayment',
        ]);

        $this->dispatch(
            'close-modal',
            name: 'payBill'
        );

        $this->notification()->success(
            'Payment Submitted',
            'Your payment proof is now waiting for admin verification.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | QR Code
    |--------------------------------------------------------------------------
    */

    public function getSelectedQrCodeProperty()
    {
        if (
            ! $this->paymentMethod
            || $this->paymentMethod === 'cash'
        ) {
            return null;
        }

        return PaymentQrCode::query()
            ->where(
                'payment_method',
                $this->paymentMethod
            )
            ->where(
                'is_active',
                true
            )
            ->latest()
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Payment Form
    |--------------------------------------------------------------------------
    */

    public function resetPaymentForm(): void
    {
        $this->reset([
            'billingId',
            'paymentMethod',
            'referenceNo',
            'proofOfPayment',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view(
            'livewire.client.my-bills-page'
        );
    }
}