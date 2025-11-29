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
        Schema::create('aprovados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carreira_id')->constrained('carreiras')->onDelete('cascade');
            $table->foreignId('edital_id')->nullable()->constrained('editais')->onDelete('set null');
            $table->string('nome', 200);
            $table->integer('posicao')->nullable();
            $table->year('ano');
            $table->timestamps();
            
            $table->index('carreira_id');
            $table->index('edital_id');
            $table->index('ano');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aprovados');
    }
};
