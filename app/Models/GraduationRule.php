<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduationRule extends Model
{
    use HasFactory;

    protected $table = 'graduation_rules';

    protected $fillable = [
        'attribute',
        'operator',
        'value',
        'value_text',
        'category',
        'priority',
    ];
}
