<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'semester_id',
        'classroom_id',
        'subject_id',
        'guru_id',
        'day_of_week',
        'jam_ke_mulai',
        'jam_ke_selesai',
        'ruangan'
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
    
}
