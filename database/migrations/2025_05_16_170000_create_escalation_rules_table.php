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
        Schema::create('escalation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->integer('hours_until_escalation');
            $table->foreignId('escalate_to_user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('notify_supervisor')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Índice compuesto para búsqueda eficiente
            $table->index(['department_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_rules');
    }
};
