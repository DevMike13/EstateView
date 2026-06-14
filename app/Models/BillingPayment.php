<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingPayment extends Model
{
    use HasFactory;

     protected $fillable = [
        'billing_id',
        'purchase_account_id',
        'user_id',
        'amount',
        'payment_method',
        'reference_no',
        'proof_of_payment',
        'status',
        'remarks',
        'paid_at',
        'verified_at',
        'verified_by',
        'source'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    public function purchaseAccount()
    {
        return $this->belongsTo(PurchaseAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
