<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar los seeders en el orden correcto
        $this->call([
            UsersTableSeeder::class,
            DepartmentSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
