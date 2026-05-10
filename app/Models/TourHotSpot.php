<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourHotSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'scene_id',
        'target_scene_id',
        'label',
        'pitch',
        'yaw',
    ];

    public function scene()
    {
        return $this->belongsTo(TourScene::class);
    }

    public function targetScene()
    {
        return $this->belongsTo(
            TourScene::class,
            'target_scene_id'
        );
    }
}
