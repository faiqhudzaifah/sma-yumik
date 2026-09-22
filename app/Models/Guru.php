<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'nuptk',
        'front_title',
        'back_title',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'phone',
        'address',
        'employment_status',
        'photo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function waliClassrooms()
    {
        return $this->hasMany(Classroom::class, 'wali_kelas_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'guru_id');
    }
}
