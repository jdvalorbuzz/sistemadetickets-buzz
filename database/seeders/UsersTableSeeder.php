<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);
        
        // Admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        // Soporte
        User::create([
            'name' => 'Soporte Técnico',
            'email' => 'soporte@example.com',
            'password' => Hash::make('password'),
            'role' => 'support',
        ]);
        
        // Cliente 1
        User::create([
            'name' => 'Cliente Ejemplo',
            'email' => 'cliente@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);
        
        // Cliente 2
        User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);
    }
}
