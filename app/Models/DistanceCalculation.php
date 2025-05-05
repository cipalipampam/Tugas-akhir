<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistanceCalculation extends Model
{
    protected $table = 'distance_calculations';

    protected $fillable = ['test_student_id', 'training_data_id', 'distance'];

    public function testStudent()
    {
        return $this->belongsTo(Student::class, 'test_student_id');
    }

    public function trainingStudent()
    {
        return $this->belongsTo(Student::class, 'training_data_id');
    }

    public function weightCalculation()
    {
        return $this->hasOne(WeightCalculation::class, 'distance_calculation_id');
    }
}
