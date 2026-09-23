<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'assignment_id',
        'siswa_id',
        'submitted_text',
        'file_path',
        'submitted_at',
        'score',
        'feedback'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'score'        => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
