<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'map_id',
        'name',
        'coords',
        'status',
        'type',
        'image',
    ];

    public function map()
    {
        return $this->belongsTo(Map::class);
    }
}
