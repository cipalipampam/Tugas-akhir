<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightCalculation extends Model
{
    protected $table = 'weight_calculations';

    protected $fillable = ['distance_calculation_id', 'weight'];

    public function distanceCalculation()
    {
        return $this->belongsTo(DistanceCalculation::class, 'distance_calculation_id');
    }
}
