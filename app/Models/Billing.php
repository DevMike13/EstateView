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

        'early_payment_discount',
        'monthly_penalty_rate',
        'penalty_amount',
        'discount_amount',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',

        'early_payment_discount' => 'decimal:2',
        'monthly_penalty_rate' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
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

    public function getRemainingBalanceAttribute(): float
    {
        return max(
            (float) $this->amount_due - (float) $this->amount_paid,
            0
        );
    }

    public function getMonthsOverdueAttribute(): int
    {
        if ($this->status === 'paid') {
            return 0;
        }

        $today = now()->startOfDay();
        $dueDate = $this->due_date->copy()->startOfDay();

        if ($today->lte($dueDate)) {
            return 0;
        }

        $daysLate = $dueDate->diffInDays($today);

        return max(
            1,
            (int) ceil($daysLate / 30)
        );
    }

    public function getCalculatedPenaltyAttribute(): float
    {
        if ($this->months_overdue <= 0) {
            return 0;
        }

        $rate = (float) $this->monthly_penalty_rate / 100;

        return round(
            $this->remaining_balance
            * $rate
            * $this->months_overdue,
            2
        );
    }

    public function getCalculatedDiscountAttribute(): float
    {
        if ($this->status === 'paid') {
            return 0;
        }

        $today = now()->startOfDay();
        $dueDate = $this->due_date->copy()->startOfDay();

        if ($today->lt($dueDate)) {
            return min(
                (float) $this->early_payment_discount,
                $this->remaining_balance
            );
        }

        return 0;
    }

    public function getPayableAmountAttribute(): float
    {
        return max(
            round(
                $this->remaining_balance
                + $this->calculated_penalty
                - $this->calculated_discount,
                2
            ),
            0
        );
    }
}
