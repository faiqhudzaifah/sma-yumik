<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'schedule_id',
        'title',
        'description',
        'type',
        'start_at',
        'end_at',
        'duration',
        'attempt_limit',
        'shuffle_questions',
        'status'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'shuffle_questions' => 'boolean',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
