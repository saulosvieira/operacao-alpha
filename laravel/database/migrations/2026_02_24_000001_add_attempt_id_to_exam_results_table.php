<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->foreignId('attempt_id')
                ->nullable()
                ->after('id')
                ->constrained('attempts')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropForeign(['attempt_id']);
            $table->dropColumn('attempt_id');
        });
    }
};
