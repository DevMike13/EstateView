<?php

namespace App\Livewire\Client;

use App\Models\HouseModel;
use App\Models\Lot;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cost Breakdown')]
class CostBreakdownPage extends Component
{
    // Form States
    public $reservationType = 'House & Lot';
    public $houseModelId;
    public $lotLocationId;
    public $paymentOption;
    public $loanTerm = 15;

    public $downpaymentPercentage = 20;

    // Dynamic Endpoints
    public $lotApiUrl;
    
    // Summary Result States
    public $totalContractPrice = 0;
    public $housePrice = 0;
    public $lotPrice = 0;
    public $downpaymentAmount = 0;
    public $loanableAmount = 0;
    public $monthlyAmortization = 0;
    public $cashDiscount = 0;

    public function mount()
    {
        if (empty($this->reservationType)) {
            $this->reservationType = 'House & Lot';
        }
        $this->updateLotApiUrl($this->reservationType);
    }

    public function updatedReservationType($value)
    {
        $this->updateLotApiUrl($value);
        $this->lotLocationId = null;
        $this->houseModelId = null;
        $this->calculateCosts();
    }

    public function updatedHouseModelId()
    {
        $this->calculateCosts();
    }

    public function updatedLotLocationId($value)
    {
        if ($value) {
            $lot = Lot::with('houseModel')->find($value);
            
            // Safety Check: If the selected lot type doesn't match, drop it
            if (!$lot || $lot->type !== $this->reservationType) {
                $this->lotLocationId = null;
            } 
            // Smart Quality-of-Life Feature: If a chosen lot is bound to an exact house model, auto-select it!
            elseif ($this->reservationType === 'House & Lot' && $lot->house_model_id) {
                $this->houseModelId = $lot->house_model_id;
            }
        }
        
        $this->calculateCosts();
    }

    public function updatedPaymentOption($value)
    {
        // Restore the default DP when bank loan is selected.
        if ($value === 'bank-loan' && !$this->downpaymentPercentage) {
            $this->downpaymentPercentage = 20;
        }

        $this->calculateCosts();
    }

    public function updatedLoanTerm()
    {
        $this->calculateCosts();
    }

    public function updatedDownpaymentPercentage($value)
    {
        // Keep the slider value within the permitted range.
        $this->downpaymentPercentage = max(
            10,
            min(80, (int) $value)
        );

        $this->calculateCosts();
    }

    public function setDownpaymentPercentage($percentage)
    {
        $this->downpaymentPercentage = max(
            10,
            min(80, (int) $percentage)
        );

        $this->calculateCosts();
    }

    public function calculateCosts()
    {
        // Reset calculations.
        $this->housePrice = 0;
        $this->lotPrice = 0;
        $this->totalContractPrice = 0;
        $this->downpaymentAmount = 0;
        $this->loanableAmount = 0;
        $this->monthlyAmortization = 0;
        $this->cashDiscount = 0;

        // Fetch lot pricing.
        if ($this->lotLocationId) {
            $lot = Lot::find($this->lotLocationId);

            if ($lot) {
                $this->lotPrice = (float) $lot->price;
            }
        }

        // Fetch house pricing for House & Lot reservations.
        if (
            $this->reservationType === 'House & Lot' &&
            $this->houseModelId
        ) {
            $house = HouseModel::find($this->houseModelId);

            if ($house) {
                $this->housePrice = (float) $house->price;
            }
        }

        // Gross contract price before payment-scheme adjustments.
        $this->totalContractPrice =
            $this->lotPrice + $this->housePrice;

        if ($this->totalContractPrice <= 0) {
            return;
        }

        if ($this->paymentOption === 'cash') {

            $this->calculateCashPayment();

        } elseif ($this->paymentOption === 'bank-loan') {

            $this->calculateBankLoan();

        } elseif ($this->paymentOption === 'deferred-payment') {

            $this->calculateDeferredPayment();
        }
    }

    protected function calculateCashPayment()
    {
        $cashDiscountRate = 0.10;

        $this->cashDiscount =
            $this->totalContractPrice * $cashDiscountRate;

        $this->totalContractPrice -= $this->cashDiscount;
    }

    protected function calculateBankLoan()
    {
        $downpaymentRate =
            ((float) $this->downpaymentPercentage) / 100;

        $this->downpaymentAmount =
            $this->totalContractPrice * $downpaymentRate;

        // Subtracting the DP is safer than separately calculating
        // another percentage because both amounts always total correctly.
        $this->loanableAmount =
            $this->totalContractPrice - $this->downpaymentAmount;

        $this->calculateMonthlyAmortization();
    }

    protected function calculateMonthlyAmortization()
    {
        if (
            $this->loanableAmount <= 0 ||
            (int) $this->loanTerm <= 0
        ) {
            $this->monthlyAmortization = 0;

            return;
        }

        $annualRate = 0.07;
        $monthlyRate = $annualRate / 12;
        $totalMonths = ((int) $this->loanTerm) * 12;

        if ($monthlyRate <= 0) {
            $this->monthlyAmortization =
                $this->loanableAmount / $totalMonths;

            return;
        }

        $compoundFactor = pow(
            1 + $monthlyRate,
            $totalMonths
        );

        $this->monthlyAmortization =
            $this->loanableAmount *
            (($monthlyRate * $compoundFactor) /
            ($compoundFactor - 1));
    }

    protected function calculateDeferredPayment()
    {
        /*
        |--------------------------------------------------------------------------
        | DEFERRED PAYMENT
        |--------------------------------------------------------------------------
        |
        | Existing ledger behavior:
        | Total contract price is divided over 36 months.
        |
        */

        $months = 36;

        $this->downpaymentAmount = 0;

        $this->loanableAmount = 0;

        $this->cashDiscount = 0;

        $this->monthlyAmortization =
            $this->totalContractPrice / $months;
    }

    protected function updateLotApiUrl($value)
    {
        $this->lotApiUrl = route('api.lots.index', [
            'type' => $value,
        ]);

        $this->dispatch(
            'lotUrlUpdated',
            url: $this->lotApiUrl
        );
    }

    public function render()
    {
        return view('livewire.client.cost-breakdown-page');
    }
}
