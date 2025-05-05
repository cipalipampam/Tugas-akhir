<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';

    protected $fillable = ['nisn', 'name', 'jenis_data', 'true_status'];

    public function studentValues()
    {
        return $this->hasMany(StudentValue::class, 'student_id');
    }

    public function trainingDistanceCalculations()
    {
        return $this->hasMany(DistanceCalculation::class, 'training_data_id');
    }

    public function testDistanceCalculations()
    {
        return $this->hasMany(DistanceCalculation::class, 'test_student_id');
    }

    public function prediction()
    {
        return $this->hasOne(Prediction::class, 'test_student_id');
    }

}
