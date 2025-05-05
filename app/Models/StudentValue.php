<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentValue extends Model
{
    protected $table = 'student_values';

    protected $fillable = ['student_id', 'key', 'value'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
