<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_account_id',
        'billing_no',
        'title',
        'due_date',
        'amount_due',
        'amount_paid',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function purchaseAccount()
    {
        return $this->belongsTo(PurchaseAccount::class);
    }

    public function payments()
    {
        return $this->hasMany(BillingPayment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(BillingPayment::class)->latestOfMany();
    }
}
