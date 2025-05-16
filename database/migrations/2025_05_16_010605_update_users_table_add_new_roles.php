<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Esta migración se ejecutará después de que el enum ya haya sido actualizado
        // SQLite no permite ORDER BY y LIMIT en UPDATE, así que usamos una subconsulta
        $firstAdmin = DB::table('users')->where('role', 'admin')->orderBy('id')->first();
        
        if ($firstAdmin) {
            DB::table('users')
                ->where('id', $firstAdmin->id)
                ->update(['role' => 'super_admin']);
        }
        
        // NOTA: También podríamos crear un usuario de soporte como ejemplo
        // DB::table('users')->insert([
        //     'name' => 'Soporte Técnico',
        //     'email' => 'soporte@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'support',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir super admin a admin
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");
    }
};
