<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'lot_id',
        'user_id',
        'agent_id',
        'status',
        'notes',
        'house_model_id',
        'reserved_at',
        'downpayment_percentage',
        'downpayment_term_months',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function preferredPayment()
    {
        return $this->hasOne(PreferredPayment::class);
    }

    public function requiredDocuments()
    {
        return $this->hasMany(RequiredDocument::class);
    }

    public function houseModel()
    {
        return $this->belongsTo(HouseModel::class, 'house_model_id');
    }

    public function reservationPayments()
    {
        return $this->hasMany(ReservationPayment::class);
    }

    public function latestReservationPayment()
    {
        return $this->hasOne(ReservationPayment::class)
            ->latestOfMany();
    }

    public function purchaseAccount()
    {
        return $this->hasOne(PurchaseAccount::class);
    }

    public function commissionRequests()
    {
        return $this->hasMany(
            CommissionRequest::class,
            'lot_reservation_id'
        );
    }
}
