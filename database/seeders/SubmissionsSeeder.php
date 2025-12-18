<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Submission;
use App\Models\Assignment;
use App\Models\User;

class SubmissionsSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Submission::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $assignment = Assignment::first();
        $students = User::where('role_id', 3)->get();

        if ($assignment) {
            $i = 0;
            foreach ($students as $s) {
                Submission::create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $s->id,
                    'content' => 'Jawaban contoh oleh ' . $s->name,
                    'submitted_at' => now()->subDays($i),
                    'score' => $i === 0 ? null : 85.5, // one ungraded, one graded
                    'grader_id' => $i === 0 ? null : 1,
                    'graded_at' => $i === 0 ? null : now()->subDay(),
                ]);
                $i++;
            }
        }
    }
}
