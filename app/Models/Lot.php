<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'map_id',
        'block_id',
        'name',
        'lot_number',
        'coords',
        'geo_coords',
        'status',
        'type',
        'price',
        'lot_area',
        'is_under_construction',
        'user_id',
        'house_model_id',
        'image',
    ];

    protected $casts = [
        'geo_coords' => 'array',
    ];

    public function map()
    {
        return $this->belongsTo(Map::class);
    }

    public function houseModel()
    {
        return $this->belongsTo(HouseModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(LotReservation::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }
}
