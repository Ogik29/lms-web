<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'subject_id',
        'student_id',
        'grader_id',
        'title',
        'description',
        'score',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function student()
    {
        // Murid yang dinilai
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function grader()
    {
        // Guru yang memberikan nilai
        return $this->belongsTo(User::class, 'grader_id', 'id');
    }
}
