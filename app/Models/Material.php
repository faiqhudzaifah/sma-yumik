<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'schedule_id',
        'title',
        'description',
        'file_path',
        'external_link',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
