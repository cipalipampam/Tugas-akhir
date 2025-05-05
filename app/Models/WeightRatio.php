<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightRatio extends Model
{
    protected $table = 'weight_ratios';

    protected $fillable = ['test_student_id', 'class', 'weight_ratio'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'test_student_id');
    }
}
