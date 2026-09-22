<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['assessment_id', 'siswa_id', 'started_at', 'submitted_at', 'score', 'attempt_number'];
    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
