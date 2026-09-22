<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'semester_id',
        'guru_id',
        'name',
        'code',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
