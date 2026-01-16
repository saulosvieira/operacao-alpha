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
        Schema::create('import_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('import_sessions')->onDelete('cascade');
            $table->integer('total_processed')->default(0);
            $table->integer('successful_imports')->default(0);
            $table->integer('failed_imports')->default(0);
            $table->json('errors')->nullable();
            $table->json('success_details')->nullable();
            $table->integer('processing_time')->nullable(); // in seconds
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_results');
    }
};
