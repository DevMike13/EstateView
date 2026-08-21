<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_id',
        'first_name', 
        'middle_name', 
        'last_name', 
        'phone', 

        'commission_percentage',
        'professional_agent_id',
        'real_estate_license_number',

        'region',
        'province',
        'municipality',
        'barangay',
        'state'
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
