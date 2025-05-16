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
        Schema::create('mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentioned_by')->constrained('users')->onDelete('cascade');
            $table->morphs('mentionable'); // Ya crea el índice automáticamente
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Índice para búsqueda rápida
            $table->index('user_id');
            // No necesitamos crear el índice mentionable_type_mentionable_id porque morphs() ya lo crea
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentions');
    }
};
