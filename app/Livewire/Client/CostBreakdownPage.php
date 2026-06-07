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

    public function updatedPaymentOption()
    {
        $this->calculateCosts();
    }

    public function updatedLoanTerm()
    {
        $this->calculateCosts();
    }

    public function calculateCosts()
    {
        // Reset calculations
        $this->housePrice = 0;
        $this->lotPrice = 0;
        $this->totalContractPrice = 0;
        $this->downpaymentAmount = 0;
        $this->loanableAmount = 0;
        $this->monthlyAmortization = 0;
        $this->cashDiscount = 0;

        // Fetch Lot Pricing
        if ($this->lotLocationId) {
            $lot = Lot::find($this->lotLocationId);
            if ($lot) {
                $this->lotPrice = (float) $lot->price;
            }
        }

        // Fetch House Pricing (Only if using House & Lot option)
        if ($this->reservationType === 'House & Lot' && $this->houseModelId) {
            $house = HouseModel::find($this->houseModelId);
            if ($house) {
                $this->housePrice = (float) $house->price;
            }
        }

        // Calculate Gross Value
        $this->totalContractPrice = $this->lotPrice + $this->housePrice;

        if ($this->totalContractPrice <= 0) {
            return;
        }

        // Calculate Payment Schemes
        if ($this->paymentOption === 'cash') {
            $this->cashDiscount = $this->totalContractPrice * 0.10; // 10% Cash Outright Discount
            $this->totalContractPrice -= $this->cashDiscount;
        } 
        
        elseif ($this->paymentOption === 'bank-loan') {
            $this->downpaymentAmount = $this->totalContractPrice * 0.20; // 20% DP Equity
            $this->loanableAmount = $this->totalContractPrice * 0.80;    // 80% Loanable Balance

            // Standard Compound Financial Formula (Amortization Equation)
            $annualRate = 0.07; // 7% Annual Fixed Interest
            $monthlyRate = $annualRate / 12;
            $totalMonths = $this->loanTerm * 12;

            if ($monthlyRate > 0) {
                $this->monthlyAmortization = $this->loanableAmount * ($monthlyRate * pow(1 + $monthlyRate, $totalMonths)) / 
                    (pow(1 + $monthlyRate, $totalMonths) - 1);
            }
        }
    }

    protected function updateLotApiUrl($value)
    {
        $this->lotApiUrl = route('api.lots.index', ['type' => $value]);
        $this->dispatch('lotUrlUpdated', url: $this->lotApiUrl);
    }

    public function render()
    {
        return view('livewire.client.cost-breakdown-page');
    }
}
