<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Str;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Course::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $teacher = User::where('role_id', 2)->first();

        if (! $teacher) return;

        Course::create([
            'name' => 'Aljabar Dasar',
            'description' => 'Kelas aljabar tingkat dasar.',
            'code' => Str::upper(Str::random(8)),
            'teacher_id' => $teacher->id,
        ]);
    }
}
