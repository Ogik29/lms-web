<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'total_score',
        'max_score',
        'is_graded',
        'graded_by',
        'graded_at',
        'time_taken_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'total_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_graded' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function answers()
    {
        return $this->hasMany(StudentQuizAnswer::class, 'attempt_id');
    }

    public function isCompleted()
    {
        return !is_null($this->submitted_at);
    }

    public function isPassed()
    {
        if (!$this->isCompleted() || is_null($this->total_score)) {
            return false;
        }

        $passingScore = $this->quiz->passing_score;
        if (is_null($passingScore)) {
            return true; // no passing score requirement
        }

        return $this->total_score >= $passingScore;
    }

    public function getPercentageScore()
    {
        if ($this->max_score == 0) {
            return 0;
        }
        return ($this->total_score / $this->max_score) * 100;
    }
}
