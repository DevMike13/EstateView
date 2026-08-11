<?php

namespace App\Livewire\FilPages;

use App\Models\AccountLedger;
use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\LotReservation;
use App\Models\PurchaseAccount;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;
use Livewire\Attributes\Url;

class ClientLedgers extends Component
{
    use WithFilePond, Actions, WithFileUploads;
    
    #[Url]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $paymentSchemeFilter = '';

    #[Url]
    public $paymentReviewFilter = '';

    public $reservationId;
    public $loanTermYears = 15;
    public $dueDate;

    public $accountId;
    public $paymentAmount;
    public $paymentDescription;
    public $accountStatus;

    public $billingId;

    public $officePaymentMethod;
    public $officeReferenceNo;
    public $officeProofOfPayment;

    public function mount()
    {
        $this->dueDate = now()->addMonth()->format('Y-m-d');
    }

    public function getApprovedReservationsProperty()
    {
        return LotReservation::with([
                'user',
                'lot',
                'houseModel',
                'preferredPayment',
                'latestReservationPayment',
            ])
            ->where('status', 'approved')
            ->whereDoesntHave('purchaseAccount')
            ->latest()
            ->get();
    }

    private function normalizePaymentScheme(?string $paymentType): string
    {
        return match ($paymentType) {
            'cash', 'Cash' => 'cash',
            'bank_loan', 'Loanable', 'Bank Loan' => 'bank_loan',
            'deferred_payment', 'Deferred', 'Deferred Payment' => 'deferred_payment',
            default => 'bank_loan',
        };
    }

    public function createLedger()
    {
        $this->validate([
            'reservationId' => 'required|exists:lot_reservations,id',
            'loanTermYears' => 'nullable|integer|min:1|max:30',
            'dueDate' => 'required|date',
        ]);

        DB::transaction(function () {
            $reservation = LotReservation::with([
                    'lot',
                    'houseModel',
                    'preferredPayment',
                    'latestReservationPayment',
                ])
                ->where('status', 'approved')
                ->whereDoesntHave('purchaseAccount')
                ->findOrFail($this->reservationId);

            $paymentScheme = $this->normalizePaymentScheme(
                $reservation->preferredPayment?->payment_type
            );

            $lotPrice = (float) $reservation->lot->price;

            $housePrice = $reservation->type === 'House & Lot'
                ? (float) ($reservation->houseModel?->price ?? 0)
                : 0;

            $tcp = $lotPrice + $housePrice;
            $reservationFee = (float) ($reservation->latestReservationPayment?->amount ?? 0);

            $cashDiscount = 0;
            $netContractPrice = $tcp;
            $downpayment = 0;
            $remainingDownpayment = 0;
            $loanableAmount = 0;
            $monthlyAmortization = 0;
            $remainingBalance = 0;
            $status = 'active';

            if ($paymentScheme === 'cash') {
                $cashDiscount = $tcp * 0.10;
                $netContractPrice = $tcp - $cashDiscount;
                $remainingBalance = max($netContractPrice - $reservationFee, 0);

                $status = $remainingBalance <= 0 ? 'fully_paid' : 'active';
            }

            if ($paymentScheme === 'bank_loan') {
                $downpaymentPercentage = (float) ($reservation->downpayment_percentage ?? 20);
                $downpaymentRate = $downpaymentPercentage / 100;

                $downpayment = $tcp * $downpaymentRate;
                $loanableAmount = $tcp - $downpayment;

                $remainingDownpayment = max($downpayment - $reservationFee, 0);

                $dpTermMonths = (int) ($reservation->downpayment_term_months ?? 12);

                $monthlyAmortization = $remainingDownpayment > 0
                    ? $remainingDownpayment / $dpTermMonths
                    : 0;

                $remainingBalance = $remainingDownpayment;

                $status = $remainingDownpayment <= 0
                    ? 'bank_processing'
                    : 'downpayment_pending';
            }

            if ($paymentScheme === 'deferred_payment') {
                $netContractPrice = $tcp;
                $remainingBalance = max($netContractPrice - $reservationFee, 0);
                $monthlyAmortization = $remainingBalance / 36;

                $status = $remainingBalance <= 0 ? 'fully_paid' : 'active';
            }

            $account = PurchaseAccount::create([
                'lot_reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'lot_id' => $reservation->lot_id,
                'house_model_id' => $reservation->house_model_id,

                'payment_scheme' => $paymentScheme,

                'lot_price' => $lotPrice,
                'house_price' => $housePrice,
                'total_contract_price' => $tcp,

                'cash_discount' => $cashDiscount,
                'net_contract_price' => $netContractPrice,

                'downpayment_amount' => $downpayment,
                'reservation_fee_credit' => $reservationFee,
                'remaining_downpayment' => $remainingDownpayment,

                'loanable_amount' => $loanableAmount,
                'loan_term_years' => match ($paymentScheme) {
                    'bank_loan' => $this->loanTermYears,
                    'deferred_payment' => 3,
                    default => null,
                },
                'monthly_amortization' => $monthlyAmortization,

                'total_paid' => $reservationFee,
                'remaining_balance' => $remainingBalance,

                'status' => $status,
            ]);

            AccountLedger::create([
                'purchase_account_id' => $account->id,
                'type' => 'debit',
                'description' => 'Total Contract Price',
                'amount' => $netContractPrice,
                'balance_after' => $netContractPrice,
            ]);

            AccountLedger::create([
                'purchase_account_id' => $account->id,
                'type' => 'credit',
                'description' => 'Reservation Fee Credit',
                'amount' => $reservationFee,
                'balance_after' => max($netContractPrice - $reservationFee, 0),
            ]);

            if ($paymentScheme === 'cash' && $remainingBalance > 0) {
                Billing::create([
                    'purchase_account_id' => $account->id,
                    'billing_no' => 'CASH-' . now()->format('YmdHis') . '-' . $account->id,
                    'title' => 'Cash Balance',
                    'due_date' => $this->dueDate,
                    'amount_due' => $remainingBalance,
                    'amount_paid' => 0,
                    'status' => 'unpaid',
                ]);
            }

            if ($paymentScheme === 'bank_loan') {
                $dpTermMonths = (int) ($reservation->downpayment_term_months ?? 12);

                $monthlyDownpayment = $remainingDownpayment > 0
                    ? $remainingDownpayment / $dpTermMonths
                    : 0;

                $startDate = \Carbon\Carbon::parse($this->dueDate);

                for ($month = 1; $month <= $dpTermMonths; $month++) {
                    Billing::create([
                        'purchase_account_id' => $account->id,
                        'billing_no' => 'DP-' . $account->id . '-' . str_pad($month, 3, '0', STR_PAD_LEFT),
                        'title' => 'Downpayment #' . $month,
                        'due_date' => $startDate->copy()->addMonths($month - 1),
                        'amount_due' => $monthlyDownpayment,
                        'amount_paid' => 0,
                        'status' => 'unpaid',
                    ]);
                }
            }

            if ($paymentScheme === 'deferred_payment') {
                $startDate = \Carbon\Carbon::parse($this->dueDate);

                for ($month = 1; $month <= 36; $month++) {
                    Billing::create([
                        'purchase_account_id' => $account->id,
                        'billing_no' => 'DEF-' . $account->id . '-' . str_pad($month, 3, '0', STR_PAD_LEFT),
                        'title' => 'Deferred Payment #' . $month,
                        'due_date' => $startDate->copy()->addMonths($month - 1),
                        'amount_due' => $monthlyAmortization,
                        'amount_paid' => 0,
                        'status' => 'unpaid',
                    ]);
                }
            }
        });

        Notification::make()
            ->title('Client Ledger Created')
            ->body('Purchase account, ledger, and billing schedule were created successfully.')
            ->success()
            ->send();

        $this->reset([
            'reservationId',
            'loanTermYears',
        ]);

        $this->loanTermYears = 15;
        $this->dueDate = now()->addMonth()->format('Y-m-d');

        $this->dispatch('close-modal', name: 'createLedger');
    }

    public function recordOfficePayment()
    {
        $this->validate([
            'accountId' => 'required|exists:purchase_accounts,id',
            'billingId' => 'required|exists:billings,id',

            'paymentAmount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'paymentDescription' => [
                'nullable',
                'string',
                'max:255',
            ],

            'officePaymentMethod' => [
                'required',
                'in:cash,bank_transfer,gcash,maya',
            ],

            'officeReferenceNo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'officeProofOfPayment' => [
                'nullable',
                'file',
                'max:20480',
            ],
        ]);

        DB::transaction(function () {

            $account = PurchaseAccount::query()
                ->lockForUpdate()
                ->findOrFail($this->accountId);

            $billing = Billing::query()
                ->where(
                    'purchase_account_id',
                    $account->id
                )
                ->whereKey($this->billingId)
                ->whereIn(
                    'status',
                    ['unpaid', 'partial']
                )
                ->lockForUpdate()
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Current billing adjustment snapshot
            |--------------------------------------------------------------------------
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
            * Admin should not be able to record more than
            * the current adjusted payable amount.
            */
            $enteredAmount =
                (float) $this->paymentAmount;

            abort_if(
                $enteredAmount > $payableAmount,
                422,
                'Payment amount exceeds the current payable amount.'
            );

            /*
            * For your current office-payment design,
            * normally this should equal payableAmount.
            *
            * Partial payment is still allowed.
            */
            $amountToApply = min(
                $enteredAmount,
                $payableAmount
            );

            /*
            |--------------------------------------------------------------------------
            | Proof
            |--------------------------------------------------------------------------
            */

            $proofPath = null;

            if ($this->officeProofOfPayment) {
                $proofPath =
                    $this->officeProofOfPayment
                        ->store(
                            "billing-payments/{$billing->id}",
                            'public'
                        );
            }

            /*
            |--------------------------------------------------------------------------
            | Determine billing completion
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | If the bill gets an early discount, paying ₱9,800 on a
            | ₱10,000 original bill must still mark it PAID.
            |
            | Likewise if overdue, paying the adjusted amount including
            | penalty must settle the bill.
            */

            $fullySettled =
                $amountToApply >= $payableAmount;

            /*
            * amount_paid represents actual money received.
            */
            $newAmountPaid =
                (float) $billing->amount_paid
                + $amountToApply;

            $billing->update([
                'amount_paid' =>
                    $newAmountPaid,

                /*
                * Snapshot latest adjustment on billing as well.
                */
                'discount_amount' =>
                    $fullySettled
                        ? $discountAmount
                        : 0,

                'penalty_amount' =>
                    $fullySettled
                        ? $penaltyAmount
                        : $penaltyAmount,

                'status' =>
                    $fullySettled
                        ? 'paid'
                        : 'partial',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Purchase-account accounting
            |--------------------------------------------------------------------------
            |
            | The ACCOUNT balance represents principal contract balance.
            |
            | Discount reduces principal.
            | Penalty is additional money and should NOT reduce principal.
            */

            $principalCredit = max(
                $amountToApply
                - $penaltyAmount,
                0
            );

            /*
            * If this is a fully settled discounted billing,
            * the discount also closes the principal obligation.
            */
            if ($fullySettled) {
                $principalCredit +=
                    $discountAmount;
            }

            /*
            * Never reduce more principal than remained on this billing.
            */
            $principalCredit = min(
                $principalCredit,
                $baseAmount
            );

            $newRemainingBalance = max(
                (float) $account->remaining_balance
                - $principalCredit,
                0
            );

            $newStatus =
                $account->status;

            if (
                $account->payment_scheme === 'bank_loan'
                && $account->billings()
                    ->where(
                        'title',
                        'like',
                        'Downpayment%'
                    )
                    ->whereIn(
                        'status',
                        ['unpaid', 'partial']
                    )
                    ->doesntExist()
            ) {
                $newStatus =
                    'bank_processing';
            }

            if ($newRemainingBalance <= 0) {
                $newStatus =
                    'fully_paid';
            }

            $account->update([
                /*
                * total_paid = actual cash received
                */
                'total_paid' =>
                    (float) $account->total_paid
                    + $amountToApply,

                'remaining_balance' =>
                    $newRemainingBalance,

                'status' =>
                    $newStatus,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Billing Payment
            |--------------------------------------------------------------------------
            */

            BillingPayment::create([
                'billing_id' =>
                    $billing->id,

                'purchase_account_id' =>
                    $account->id,

                'user_id' =>
                    $account->user_id,

                /*
                * Snapshot the original obligation.
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
                * Actual money received.
                */
                'amount' =>
                    $amountToApply,

                'payment_method' =>
                    $this->officePaymentMethod,

                'reference_no' =>
                    $this->officeReferenceNo,

                'proof_of_payment' =>
                    $proofPath,

                'status' =>
                    'verified',

                'source' =>
                    'office_payment',

                'paid_at' =>
                    now(),

                'verified_at' =>
                    now(),

                'verified_by' =>
                    auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Ledger
            |--------------------------------------------------------------------------
            */

            AccountLedger::create([
                'purchase_account_id' =>
                    $account->id,

                'type' =>
                    'credit',

                'description' =>
                    $this->paymentDescription
                    ?: 'Office payment for '
                        . $billing->title,

                /*
                * actual money received
                */
                'amount' =>
                    $amountToApply,

                'balance_after' =>
                    $newRemainingBalance,
            ]);

            /*
            * Optional but useful:
            * separately record the discount.
            */
            if (
                $fullySettled
                && $discountAmount > 0
            ) {
                AccountLedger::create([
                    'purchase_account_id' =>
                        $account->id,

                    'type' =>
                        'credit',

                    'description' =>
                        'Early payment discount for '
                        . $billing->title,

                    'amount' =>
                        $discountAmount,

                    'balance_after' =>
                        $newRemainingBalance,
                ]);
            }

            /*
            * Optional:
            * Record the penalty as its own debit for auditability.
            */
            if ($penaltyAmount > 0) {
                AccountLedger::create([
                    'purchase_account_id' =>
                        $account->id,

                    'type' =>
                        'debit',

                    'description' =>
                        'Late payment penalty for '
                        . $billing->title
                        . ' ('
                        . $penaltyMonths
                        . ' period'
                        . ($penaltyMonths !== 1 ? 's' : '')
                        . ')',

                    'amount' =>
                        $penaltyAmount,

                    'balance_after' =>
                        $newRemainingBalance,
                ]);
            }
        });

        $this->reset([
            'accountId',
            'billingId',
            'paymentAmount',
            'paymentDescription',
            'officePaymentMethod',
            'officeReferenceNo',
            'officeProofOfPayment',
        ]);

        $this->dispatch(
            'close-modal',
            name: 'recordPayment'
        );

        Notification::make()
            ->title('Payment Recorded')
            ->body(
                'The office payment was recorded successfully.'
            )
            ->success()
            ->send();
    }

    public function approveBillingPayment($paymentId)
    {
        DB::transaction(function () use ($paymentId) {

            $payment =
                BillingPayment::query()
                    ->with([
                        'billing',
                        'purchaseAccount',
                    ])
                    ->lockForUpdate()
                    ->findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                return;
            }

            $billing =
                $payment->billing;

            $account =
                $payment->purchaseAccount;

            /*
            |--------------------------------------------------------------------------
            | USE SNAPSHOT FROM CLIENT SUBMISSION
            |--------------------------------------------------------------------------
            */

            $baseAmount =
                (float) (
                    $payment->base_amount
                    ?? $billing->remaining_balance
                );

            $discountAmount =
                (float) (
                    $payment->discount_amount
                    ?? 0
                );

            $penaltyAmount =
                (float) (
                    $payment->penalty_amount
                    ?? 0
                );

            $amountReceived =
                (float) $payment->amount;

            /*
            * The exact payable amount AT SUBMISSION TIME.
            */
            $expectedPayable =
                max(
                    $baseAmount
                    + $penaltyAmount
                    - $discountAmount,
                    0
                );

            $fullySettled =
                $amountReceived >=
                $expectedPayable;

            $billing->update([
                'amount_paid' =>
                    (float) $billing->amount_paid
                    + $amountReceived,

                'discount_amount' =>
                    $discountAmount,

                'penalty_amount' =>
                    $penaltyAmount,

                'status' =>
                    $fullySettled
                        ? 'paid'
                        : 'partial',
            ]);

            /*
            * Penalty doesn't reduce contract principal.
            */
            $principalCredit = max(
                $amountReceived
                - $penaltyAmount,
                0
            );

            /*
            * Discount closes the waived principal.
            */
            if ($fullySettled) {
                $principalCredit +=
                    $discountAmount;
            }

            $principalCredit = min(
                $principalCredit,
                $baseAmount
            );

            $newRemainingBalance = max(
                (float) $account->remaining_balance
                - $principalCredit,
                0
            );

            $newStatus =
                $account->status;

            if (
                $account->payment_scheme === 'bank_loan'
                && $account->billings()
                    ->where(
                        'title',
                        'like',
                        'Downpayment%'
                    )
                    ->whereIn(
                        'status',
                        ['unpaid', 'partial']
                    )
                    ->doesntExist()
            ) {
                $newStatus =
                    'bank_processing';
            }

            if ($newRemainingBalance <= 0) {
                $newStatus =
                    'fully_paid';
            }

            $account->update([
                'total_paid' =>
                    (float) $account->total_paid
                    + $amountReceived,

                'remaining_balance' =>
                    $newRemainingBalance,

                'status' =>
                    $newStatus,
            ]);

            AccountLedger::create([
                'purchase_account_id' =>
                    $account->id,

                'type' =>
                    'credit',

                'description' =>
                    'Verified client payment for '
                    . $billing->title,

                'amount' =>
                    $amountReceived,

                'balance_after' =>
                    $newRemainingBalance,
            ]);

            if (
                $fullySettled
                && $discountAmount > 0
            ) {
                AccountLedger::create([
                    'purchase_account_id' =>
                        $account->id,

                    'type' =>
                        'credit',

                    'description' =>
                        'Early payment discount for '
                        . $billing->title,

                    'amount' =>
                        $discountAmount,

                    'balance_after' =>
                        $newRemainingBalance,
                ]);
            }

            if ($penaltyAmount > 0) {
                AccountLedger::create([
                    'purchase_account_id' =>
                        $account->id,

                    'type' =>
                        'debit',

                    'description' =>
                        'Late payment penalty for '
                        . $billing->title,

                    'amount' =>
                        $penaltyAmount,

                    'balance_after' =>
                        $newRemainingBalance,
                ]);
            }

            $payment->update([
                'status' =>
                    'verified',

                'verified_at' =>
                    now(),

                'verified_by' =>
                    auth()->id(),
            ]);
        });

        Notification::make()
            ->title('Payment Approved')
            ->success()
            ->send();
    }

    public function rejectBillingPayment($paymentId)
    {
        BillingPayment::findOrFail($paymentId)->update([
            'status' => 'rejected',
        ]);

        Notification::make()
            ->title('Payment Rejected')
            ->danger()
            ->send();
    }

    public function updateAccountStatus()
    {
        $this->validate([
            'accountId' => 'required|exists:purchase_accounts,id',
            'accountStatus' => 'required|in:active,downpayment_pending,bank_processing,fully_paid,cancelled',
        ]);

        PurchaseAccount::findOrFail($this->accountId)
            ->update([
                'status' => $this->accountStatus,
            ]);

        Notification::make()
            ->title('Status Updated')
            ->success()
            ->send();

        $this->reset([
            'accountId',
            'accountStatus',
        ]);

        $this->dispatch('close-modal', name: 'changeStatus');
    }

    public function setPaymentReviewFilter(string $filter): void
    {
        $this->paymentReviewFilter = $filter;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->paymentSchemeFilter = '';
        $this->paymentReviewFilter = '';
    }

    public function render()
    {
        $accounts = PurchaseAccount::query()
            ->with([
                'user',
                'lot',
                'houseModel',
                'reservation',
                'billings' => fn ($query) => $query->orderBy('due_date'),
                'billings.payments',
                'billings.latestPayment',
                'ledgers',
            ])

            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('lot', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('reservation', function ($q) {
                        $q->where('type', 'like', '%' . $this->search . '%');
                    });
                });
            })

            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })

            ->when($this->paymentSchemeFilter, function ($query) {
                $query->where('payment_scheme', $this->paymentSchemeFilter);
            })

            ->when($this->paymentReviewFilter === 'pending_review', function ($query) {
                $query->whereHas('billings.payments', function ($q) {
                    $q->where('status', 'pending');
                });
            })

            ->when($this->paymentReviewFilter === 'has_verified', function ($query) {
                $query->whereHas('billings.payments', function ($q) {
                    $q->where('status', 'verified');
                });
            })

            ->latest()
            ->get();

        return view('livewire.fil-pages.client-ledgers', [
            'accounts' => $accounts,
        ]);
    }
}