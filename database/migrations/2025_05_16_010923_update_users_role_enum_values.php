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
        // SQLite no permite alterar columnas con constraints directamente,
        // así que tenemos que recrear la tabla con las nuevas restricciones
        
        // 1. Crear tabla temporal con nuevos valores permitidos
        Schema::create('users_temp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['super_admin', 'admin', 'support', 'client'])->default('client');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        
        // 2. Copiar datos de la tabla original a la temporal
        DB::statement("INSERT INTO users_temp SELECT * FROM users");
        
        // 3. Eliminar tabla original
        Schema::drop('users');
        
        // 4. Renombrar tabla temporal a la original
        Schema::rename('users_temp', 'users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el proceso
        Schema::create('users_temp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['admin', 'client'])->default('client');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        
        // Copiar datos (excluyendo roles que no existen en la versión anterior)
        DB::statement("INSERT INTO users_temp SELECT * FROM users WHERE role IN ('admin', 'client')");
        
        // Eliminar y renombrar
        Schema::drop('users');
        Schema::rename('users_temp', 'users');
    }
};
