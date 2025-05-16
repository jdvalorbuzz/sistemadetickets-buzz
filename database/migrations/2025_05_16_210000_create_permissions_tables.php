<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla para definir permisos individuales
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre técnico, e.g., 'view_departments'
            $table->string('display_name'); // Nombre para mostrar, e.g., 'Ver Departamentos'
            $table->string('description')->nullable(); // Descripción opcional
            $table->string('category')->nullable(); // Categoría para agrupar (tickets, departamentos, etc.)
            $table->timestamps();
            
            $table->unique('name');
        });
        
        // Tabla para asignar permisos a roles
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // super_admin, admin, support, client
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
