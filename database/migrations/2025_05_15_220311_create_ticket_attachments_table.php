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
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que subió el archivo
            $table->string('file_name'); // Nombre original del archivo
            $table->string('file_path'); // Ruta donde se almacena
            $table->string('file_type')->nullable(); // Tipo MIME
            $table->integer('file_size')->nullable(); // Tamaño en bytes
            $table->string('context')->default('ticket'); // Contexto (ticket o respuesta)
            $table->foreignId('reply_id')->nullable()->constrained()->onDelete('cascade'); // Si aplica a una respuesta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
