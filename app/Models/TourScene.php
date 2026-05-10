<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourScene extends Model
{
    use HasFactory;

    protected $fillable = [
        'virtual_tour_id',
        'name',
        'image'
    ];

    public function tour()
    {
        return $this->belongsTo(VirtualTour::class);
    }

    public function hotspots()
    {
        return $this->hasMany(
            TourHotspot::class,
            'scene_id'
        );
    }
}
