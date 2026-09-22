<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['schedule_id', 'title', 'description', 'type', 'due_date'];
    protected $casts = ['due_date' => 'datetime'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
