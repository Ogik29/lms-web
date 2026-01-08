<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'essay_answer',
        'points_earned',
        'is_correct',
    ];

    protected $casts = [
        'points_earned' => 'decimal:2',
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(StudentQuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuizQuestionOption::class, 'selected_option_id');
    }
}
