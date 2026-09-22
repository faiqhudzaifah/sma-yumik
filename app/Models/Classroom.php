<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'semester_id', // Pusat relasi yang baru
        'name',
        'room_code',
        'grade_level',
        'major',
        'capacity',
        'wali_kelas_id',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    /**
     * Kelas ini merupakan bagian dari Semester mana?
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Siapa Guru yang menjadi Wali Kelas di rombel ini?
     */
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'classroom_siswa')->withTimestamps();
    }
}
