<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualTour extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_model_id',
        'title',
    ];

    public function houseModel()
    {
        return $this->belongsTo(HouseModel::class);
    }

    public function scenes()
    {
        return $this->hasMany(TourScene::class);
    }
}
