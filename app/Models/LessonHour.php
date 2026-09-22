<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonHour extends Model
{
    protected $fillable = [
        'day_type',
        'jam_ke',
        'start_time',
        'end_time',
        'type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
