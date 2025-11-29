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
        Schema::create('respostas_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('simulado_id')->constrained('simulados')->onDelete('cascade');
            $table->foreignId('questao_id')->constrained('questoes')->onDelete('cascade');
            $table->char('resposta_escolhida', 1); // A, B, C, D ou E
            $table->boolean('correta')->default(false);
            $table->integer('tempo_resposta_segundos')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('simulado_id');
            $table->index(['user_id', 'simulado_id']);
            $table->unique(['user_id', 'simulado_id', 'questao_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respostas_usuarios');
    }
};
