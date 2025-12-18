<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * The courses that this user (as a teacher) has created.
     */
    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id', 'id');
    }

    /**
     * The courses that this user (as a student) is enrolled in.
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user')->withTimestamps();
    }

    /**
     * The schedules that this user (as a teacher) teaches.
     */
    public function schedulesTaught()
    {
        return $this->hasMany(Schedule::class, 'teacher_id', 'id');
    }

    /**
     * The grades this user (as a student) has received.
     */
    public function gradesReceived()
    {
        return $this->hasMany(Grade::class, 'student_id', 'id');
    }

    /**
     * The grades this user (as a teacher/grader) has given.
     */
    public function gradesGiven()
    {
        return $this->hasMany(Grade::class, 'grader_id', 'id');
    }
}
