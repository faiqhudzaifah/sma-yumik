<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'schedule_id',
        'user_id',
        'title',
        'content',
        'priority',
        'start_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function readBy()
    {
        return $this->belongsToMany(User::class, 'announcement_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
