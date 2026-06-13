<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_reservation_id',
        'amount',
        'payment_method',
        'reference_no',
        'proof_of_payment',
        'paid_at',
        'status',
        'remarks',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(LotReservation::class, 'lot_reservation_id');
    }
}
