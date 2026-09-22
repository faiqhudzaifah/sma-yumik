<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = ['schedule_id', 'date', 'meeting_number', 'topic', 'description'];
    protected $casts = ['date' => 'date'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
