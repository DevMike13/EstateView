<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'account_name',
        'account_number',
        'qr_image',
        'is_active',
    ];
}
