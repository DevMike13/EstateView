<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'boundary_geo_coords',
    ];

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    protected $casts = [
        'boundary_geo_coords' => 'array',
    ];

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
