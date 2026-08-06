<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentQrCode extends Model
{
    use HasFactory;

     protected $fillable = [
        'agent_id',
        'label',
        'provider_name',
        'account_name',
        'account_number',
        'qr_image_path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(
            User::class,
            'agent_id'
        );
    }

    public function getImageUrlAttribute(): string
    {
        return asset(
            'storage/' . ltrim(
                $this->qr_image_path,
                '/'
            )
        );
    }
}
