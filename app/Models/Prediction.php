<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $table = 'predictions';

    protected $fillable = ['test_student_id', 'predicted_status', 'k_value'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'test_student_id');
    }
}

