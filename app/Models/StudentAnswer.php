<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $fillable = ['submission_id', 'question_id', 'option_id', 'answer_text', 'is_correct', 'points_awarded'];
    protected $casts = ['is_correct' => 'boolean'];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
