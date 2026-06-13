<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseAccount extends Model
{
    use HasFactory;

     protected $fillable = [
        'lot_reservation_id',
        'user_id',
        'lot_id',
        'house_model_id',
        'payment_scheme',
        'lot_price',
        'house_price',
        'total_contract_price',
        'cash_discount',
        'net_contract_price',
        'downpayment_amount',
        'reservation_fee_credit',
        'remaining_downpayment',
        'loanable_amount',
        'loan_term_years',
        'monthly_amortization',
        'total_paid',
        'remaining_balance',
        'status',
    ];

    public function reservation()
    {
        return $this->belongsTo(LotReservation::class, 'lot_reservation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function houseModel()
    {
        return $this->belongsTo(HouseModel::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function ledgers()
    {
        return $this->hasMany(AccountLedger::class);
    }
}
