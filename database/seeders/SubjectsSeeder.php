<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Subject::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        Subject::create(['name' => 'Matematika']);
        Subject::create(['name' => 'Bahasa Indonesia']);
        Subject::create(['name' => 'Ilmu Pengetahuan']);
    }
}
