<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_reservation_id',
        'document_type',
        'file_path',
        'original_name',
    ];

    const TYPES = [
        '1x1 Picture',
        'Primary IDs',
        'Proof of Billing',
        'PSA Birth Certificate',
        'Proof of Income',
        'TIN ID',
    ];

    public function lotReservation()
    {
        return $this->belongsTo(LotReservation::class);
    }
}
