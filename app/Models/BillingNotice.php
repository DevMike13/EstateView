<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_id',
        'purchase_account_id',
        'user_id',
        'notice_type',
        'overdue_month',
        'amount',
        'billing_due_date',
        'notice_date',
        'deadline_date',
        'effective_date',
        'pdf_path',
        'email_to',
        'status',
        'sent_at',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'overdue_month' => 'integer',
        'billing_due_date' => 'date',
        'notice_date' => 'date',
        'deadline_date' => 'date',
        'effective_date' => 'date',
        'sent_at' => 'datetime',
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
}
