<?php

namespace App\Livewire\FilPages;

use App\Models\AccountLedger;
use App\Models\Billing;
use App\Models\LotReservation;
use App\Models\PurchaseAccount;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClientLedgers extends Component
{
    public $reservationId;
    public $loanTermYears = 15;
    public $dueDate;

    public $accountId;
    public $paymentAmount;
    public $paymentDescription;
    public $accountStatus;

    public $billingId;

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
                $downpayment = $tcp * 0.20;
                $loanableAmount = $tcp * 0.80;
                $remainingDownpayment = max($downpayment - $reservationFee, 0);

                $annualRate = 0.07;
                $monthlyRate = $annualRate / 12;
                $totalMonths = $this->loanTermYears * 12;

                $monthlyAmortization = $loanableAmount *
                    (
                        ($monthlyRate * pow(1 + $monthlyRate, $totalMonths))
                        /
                        (pow(1 + $monthlyRate, $totalMonths) - 1)
                    );

                $remainingBalance = $remainingDownpayment + $loanableAmount;

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
                if ($remainingDownpayment > 0) {
                    Billing::create([
                        'purchase_account_id' => $account->id,
                        'billing_no' => 'DP-' . now()->format('YmdHis') . '-' . $account->id,
                        'title' => 'Remaining Downpayment',
                        'due_date' => $this->dueDate,
                        'amount_due' => $remainingDownpayment,
                        'amount_paid' => 0,
                        'status' => 'unpaid',
                    ]);
                }

                $startDate = \Carbon\Carbon::parse($this->dueDate)->addMonth();
                $totalMonths = $this->loanTermYears * 12;

                for ($month = 1; $month <= $totalMonths; $month++) {
                    Billing::create([
                        'purchase_account_id' => $account->id,
                        'billing_no' => 'MA-' . $account->id . '-' . str_pad($month, 3, '0', STR_PAD_LEFT),
                        'title' => 'Monthly Amortization #' . $month,
                        'due_date' => $startDate->copy()->addMonths($month - 1),
                        'amount_due' => $monthlyAmortization,
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
            'paymentAmount' => 'required|numeric|min:1',
            'paymentDescription' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {
            $account = PurchaseAccount::findOrFail($this->accountId);

            $billing = Billing::where('purchase_account_id', $account->id)
                ->where('id', $this->billingId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->firstOrFail();

            $amount = (float) $this->paymentAmount;
            $billingBalance = $billing->amount_due - $billing->amount_paid;
            $amountToApply = min($amount, $billingBalance);

            $billing->update([
                'amount_paid' => $billing->amount_paid + $amountToApply,
                'status' => ($billing->amount_paid + $amountToApply) >= $billing->amount_due
                    ? 'paid'
                    : 'partial',
            ]);

            $newRemainingBalance = max($account->remaining_balance - $amountToApply, 0);

            $newStatus = $account->status;

            if (
                $account->payment_scheme === 'bank_loan'
                && $account->billings()
                    ->where('title', 'Remaining Downpayment')
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->doesntExist()
            ) {
                $newStatus = 'bank_processing';
            }

            if ($newRemainingBalance <= 0) {
                $newStatus = 'fully_paid';
            }

            $account->update([
                'total_paid' => $account->total_paid + $amountToApply,
                'remaining_balance' => $newRemainingBalance,
                'status' => $newStatus,
            ]);

            AccountLedger::create([
                'purchase_account_id' => $account->id,
                'type' => 'credit',
                'description' => $this->paymentDescription ?: 'Payment for ' . $billing->title,
                'amount' => $amountToApply,
                'balance_after' => $newRemainingBalance,
            ]);
        });

        $this->reset([
            'accountId',
            'billingId',
            'paymentAmount',
            'paymentDescription',
        ]);

        $this->dispatch('close-modal', name: 'recordPayment');

        Notification::make()
            ->title('Payment Recorded')
            ->success()
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

    public function render()
    {
        return view('livewire.fil-pages.client-ledgers', [
            'accounts' => PurchaseAccount::with([
                    'user',
                    'lot',
                    'houseModel',
                    'reservation',
                    'billings' => fn ($query) => $query->orderBy('due_date'),
                    'ledgers',
                ])
                ->latest()
                ->get(),
        ]);
    }
}