<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferredPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_reservation_id',
        'payment_type'
    ];

    public function lotReservation()
    {
        return $this->belongsTo(LotReservation::class);
    }
}
