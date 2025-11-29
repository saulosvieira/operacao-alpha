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
        Schema::create('resultados_simulados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('simulado_id')->constrained('simulados')->onDelete('cascade');
            $table->decimal('pontuacao', 5, 2); // Ex: 85.50
            $table->integer('total_questoes');
            $table->integer('acertos');
            $table->integer('tempo_total_segundos');
            $table->timestamp('finalizado_em');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('simulado_id');
            $table->index('finalizado_em');
            $table->index(['user_id', 'simulado_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados_simulados');
    }
};
