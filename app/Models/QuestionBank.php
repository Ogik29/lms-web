<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'question_bank';

    protected $fillable = [
        'category_id',
        'teacher_id',
        'question_text',
        'question_type',
        'points',
        'explanation',
    ];

    protected $casts = [
        'points' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(QuestionBankCategory::class, 'category_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionBankOption::class)->orderBy('order');
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'question_bank_id');
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
