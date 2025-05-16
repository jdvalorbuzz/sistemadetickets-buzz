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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('kanban_status_id')->nullable()->constrained();
            $table->integer('kanban_order')->default(0);
            
            // Índice para ordenamiento
            $table->index(['kanban_status_id', 'kanban_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['kanban_status_id']);
            $table->dropColumn(['kanban_status_id', 'kanban_order']);
        });
    }
};
