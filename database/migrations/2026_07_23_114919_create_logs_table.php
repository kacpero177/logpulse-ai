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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('service_name'); // Nazwa serwisu/aplikacji (np. AuthService)
            $table->enum('level', ['info', 'warning', 'error', 'critical']); // Poziom błędu
            $table->text('message'); // Krótkie podsumowanie błędu
            $table->longText('stack_trace')->nullable(); // Pełny stack trace
            $table->text('ai_summary')->nullable(); // Odpowiedź wygenerowana przez AI
            $table->string('status')->default('pending'); // pending, processing, analyzed, failed
            $table->timestamps();

            // Indeksy dla optymalizacji zapytania przy dużej ilości danych
            $table->index(['created_at', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};