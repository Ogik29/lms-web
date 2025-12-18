<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Role::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        Role::create(['name' => 'Admin']); // id 1
        Role::create(['name' => 'Guru']);  // id 2
        Role::create(['name' => 'Murid']); // id 3
    }
}
