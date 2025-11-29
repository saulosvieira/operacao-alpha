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
        Schema::create('editais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carreira_id')->constrained('carreiras')->onDelete('cascade');
            $table->string('titulo', 200);
            $table->text('descricao')->nullable();
            $table->date('data_publicacao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index('carreira_id');
            $table->index('ativo');
            $table->index('data_publicacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editais');
    }
};
