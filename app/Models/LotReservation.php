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
        'status',
        'notes',
        'house_model_id',
        'reserved_at',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preferredPayment()
    {
        return $this->hasOne(PreferredPayment::class);
    }

    public function requiredDocuments()
    {
        return $this->hasMany(RequiredDocument::class);
    }
}
