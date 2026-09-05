<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'created_by_role',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'name',
        'phone',
        'notes',
        'document_path',
        'document_paths',
        'status',
        'client_confirmed_at',
        'client_declined_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'client_confirmed_at' => 'datetime',
        'client_declined_at' => 'datetime',
        'document_paths' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
