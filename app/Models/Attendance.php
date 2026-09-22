<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['meeting_id', 'siswa_id', 'status', 'notes'];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
