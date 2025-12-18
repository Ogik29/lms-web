<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        User::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Teacher
        User::create([
            'name' => 'Lamine Guru',
            'email' => 'guru@example.test',
            'password' => 'password123',
            'role_id' => 2,
        ]);

        // Students
        User::create([
            'name' => 'Aura Siswa',
            'email' => 'siswa1@example.test',
            'password' => 'password123',
            'role_id' => 3,
        ]);

        User::create([
            'name' => 'Elsa Siswa',
            'email' => 'siswa2@example.test',
            'password' => 'password123',
            'role_id' => 3,
        ]);
    }
}
