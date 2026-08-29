<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'map_id',
        'name',
        'geo_coords',
    ];

    protected $casts = [
        'geo_coords' => 'array',
    ];

    public function map()
    {
        return $this->belongsTo(Map::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}