<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_date',
        'type',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'assignment_id', 'id');
    }

    public function quiz()
    {
        return $this->hasOne(\App\Models\Quiz::class);
    }

    public function isQuiz()
    {
        return $this->type === 'quiz';
    }
}
