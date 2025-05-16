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
        Schema::create('escalation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('previous_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('escalated_to_user_id')->constrained('users')->onDelete('cascade');
            $table->string('reason');
            $table->foreignId('escalation_rule_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            // Índice para búsqueda rápida
            $table->index('ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_logs');
    }
};
