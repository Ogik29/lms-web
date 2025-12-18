<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;

class EnrollmentsSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('course_user')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $course = Course::first();
        $students = User::where('role_id', 3)->get();

        foreach ($students as $s) {
            $course->students()->syncWithoutDetaching($s->id);
        }
    }
}
