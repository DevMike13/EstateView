<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'purchase_account_id',
        'lot_reservation_id',
        'period_number',
        'period_label',
        'commission_percentage',
        'total_contract_price',
        'total_commission_amount',
        'requested_amount',
        'covered_billing_ids',
        'status',
        'remarks',
        'reviewed_by',
        'requested_at',
        'reviewed_at',
        'paid_at',
        'receipt_path',
    ];

    protected $casts = [
        'covered_billing_ids' => 'array',

        'commission_percentage' => 'decimal:2',
        'total_contract_price' => 'decimal:2',
        'total_commission_amount' => 'decimal:2',
        'requested_amount' => 'decimal:2',

        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(
            User::class,
            'agent_id'
        );
    }

    public function purchaseAccount()
    {
        return $this->belongsTo(
            PurchaseAccount::class
        );
    }

    public function reservation()
    {
        return $this->belongsTo(
            LotReservation::class,
            'lot_reservation_id'
        );
    }

    public function reviewer()
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }
}
