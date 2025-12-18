<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Subject;

class SchedulesSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Schedule::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $course = Course::first();
        $subject = Subject::first();

        if ($course && $subject) {
            Schedule::create([
                'course_id' => $course->id,
                'subject_id' => $subject->id,
                'teacher_id' => $course->teacher_id,
                'day_of_week' => 'Senin',
                'start_time' => '09:00',
                'end_time' => '10:30',
            ]);
        }
    }
}
