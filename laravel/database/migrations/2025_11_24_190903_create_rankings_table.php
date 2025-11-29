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
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('pontuacao_diaria', 8, 2)->default(0);
            $table->decimal('pontuacao_semanal', 8, 2)->default(0);
            $table->date('data_calculo');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('data_calculo');
            $table->index(['pontuacao_diaria', 'data_calculo']);
            $table->index(['pontuacao_semanal', 'data_calculo']);
            $table->unique(['user_id', 'data_calculo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
