<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'academic_year_id',
        'type', // Ganjil atau Genap
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    // ==========================================
    // RELASI KE ATAS (Induk)
    // ==========================================
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ==========================================
    // RELASI KE BAWAH (Anak)
    // ==========================================
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
