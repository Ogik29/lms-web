<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'points',
        'order',
        'explanation',
    ];

    protected $casts = [
        'points' => 'decimal:2',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function answers()
    {
        return $this->hasMany(StudentQuizAnswer::class, 'question_id');
    }

    public function isMultipleChoice()
    {
        return $this->question_type === 'multiple_choice';
    }

    public function isEssay()
    {
        return $this->question_type === 'essay';
    }
}
