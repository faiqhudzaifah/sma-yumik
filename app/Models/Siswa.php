<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $fillable = [
        'user_id',
        'nisn',
        'nis',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'phone',
        'address',
        'father_name',
        'mother_name',
        'guardian_name',
        'parent_phone',
        'enrollment_year',
        'status',
        'photo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_siswa')->withTimestamps();
    }
}
