<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_account_id',
        'type',
        'description',
        'amount',
        'balance_after',
    ];

    public function purchaseAccount()
    {
        return $this->belongsTo(PurchaseAccount::class);
    }
}
