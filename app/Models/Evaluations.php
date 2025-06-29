<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluations extends Model
{
     protected $fillable = [
        'training_percentage',
        'k_value',
        'accuracy',
        'error_rate',
        'confusion_matrix',
        'precision',
        'recall',
        'f1_score',
        'training_data_count',
        'test_data_count'
    ];

    protected $casts = [
        'confusion_matrix' => 'array',
    ];
}
