<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'duration_minutes',
        'max_attempts',
        'show_results_immediately',
        'shuffle_questions',
        'shuffle_options',
        'passing_score',
    ];

    protected $casts = [
        'show_results_immediately' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(StudentQuizAttempt::class);
    }
}
