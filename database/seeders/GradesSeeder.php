<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Course;
use App\Models\User;

class GradesSeeder extends Seeder
{
    public function run(): void
    {
        Grade::truncate();

        $course = Course::first();
        $student = User::where('role_id', 3)->first();
        $teacher = User::where('role_id', 2)->first();

        if ($course && $student && $teacher) {
            Grade::create([
                'course_id' => $course->id,
                'subject_id' => rand(1, 3),
                'student_id' => $student->id,
                'grader_id' => $teacher->id,
                'title' => 'Nilai UTS',
                'description' => 'Nilai ujian tengah semester',
                'score' => 88.5,
            ]);
        }
    }
}
