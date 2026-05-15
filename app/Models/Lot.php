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
        'name',
        'coords',
        'status',
        'type',
        'price',
        'lot_area',
        'user_id',
        'house_model_id',
        'image',
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
}
