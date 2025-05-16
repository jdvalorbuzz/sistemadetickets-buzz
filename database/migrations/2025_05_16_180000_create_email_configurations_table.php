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
        Schema::create('email_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('incoming_type'); // IMAP, API, etc.
            $table->string('incoming_server');
            $table->integer('incoming_port');
            $table->string('incoming_encryption')->nullable();
            $table->string('incoming_username');
            $table->text('incoming_password');
            $table->string('outgoing_type'); // SMTP, API, etc.
            $table->string('outgoing_server');
            $table->integer('outgoing_port');
            $table->string('outgoing_encryption')->nullable();
            $table->string('outgoing_username');
            $table->text('outgoing_password');
            $table->string('from_email');
            $table->string('from_name');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('polling_interval')->default(5); // en minutos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_configurations');
    }
};
