<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'teacher_id',
    ];

    public function teacher()
    {
        // Relasi ke User sebagai Guru (pemilik kursus/kelas)
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    public function students()
    {
        // Relasi Many-to-Many ke User sebagai Murid
        return $this->belongsToMany(User::class, 'course_user')->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'course_id', 'id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'course_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(\App\Models\Assignment::class, 'course_id', 'id');
    }
}
