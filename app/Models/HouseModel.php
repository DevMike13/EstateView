<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'virtual_tour_url',
        'model_name',
        'bedrooms',
        'bathrooms',
        'floor_area',
        'price',
    ];

    protected $casts = [
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'floor_area' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    public function virtualTour()
    {
        return $this->hasOne(VirtualTour::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
