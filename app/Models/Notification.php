<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

     protected $fillable = [
        'title',
        'message',
        'type',
        'url',
        'created_by',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
