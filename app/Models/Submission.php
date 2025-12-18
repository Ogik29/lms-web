<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'content',
        'file_path',
        'submitted_at',
        'is_edited',
        'edited_at',
        'deadline',
        'score',
        'grader_id',
        'graded_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'edited_at' => 'datetime',
        'deadline' => 'datetime',
        'graded_at' => 'datetime',
        'is_edited' => 'boolean',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'grader_id', 'id');
    }
}
