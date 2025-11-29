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
        Schema::create('questoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulado_id')->constrained('simulados')->onDelete('cascade');
            $table->integer('numero_questao');
            $table->text('enunciado');
            $table->string('imagem_enunciado', 255)->nullable();
            
            // Alternativas
            $table->text('alternativa_a');
            $table->string('imagem_a', 255)->nullable();
            $table->text('alternativa_b');
            $table->string('imagem_b', 255)->nullable();
            $table->text('alternativa_c');
            $table->string('imagem_c', 255)->nullable();
            $table->text('alternativa_d');
            $table->string('imagem_d', 255)->nullable();
            $table->text('alternativa_e');
            $table->string('imagem_e', 255)->nullable();
            
            $table->char('resposta_correta', 1); // A, B, C, D ou E
            $table->text('explicacao')->nullable();
            $table->timestamps();
            
            $table->index('simulado_id');
            $table->index(['simulado_id', 'numero_questao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questoes');
    }
};
