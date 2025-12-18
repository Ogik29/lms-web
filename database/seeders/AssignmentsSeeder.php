<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\Course;
use Carbon\Carbon;

class AssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Assignment::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $course = Course::first();

        if ($course) {
            Assignment::create([
                'course_id' => $course->id,
                'title' => 'Latihan Persamaan Linear',
                'description' => 'Kerjakan soal-soal pada lampiran.',
                'due_date' => Carbon::now()->addDays(7),
            ]);

            Assignment::create([
                'course_id' => $course->id,
                'title' => 'Soal Rumah: Fungsi dan Grafik',
                'description' => 'Pelajari bab fungsi, lalu kerjakan 10 soal.',
                'due_date' => Carbon::now()->addDays(14),
            ]);
        }
    }
}
