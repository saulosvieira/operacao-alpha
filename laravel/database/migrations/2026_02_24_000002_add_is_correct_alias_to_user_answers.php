<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para MySQL, adicionar colunas faltantes para compatibilidade
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('user_answers', function (Blueprint $table) {
                // Adicionar is_correct se não existir
                if (!Schema::hasColumn('user_answers', 'is_correct')) {
                    $table->boolean('is_correct')->default(false)->after('chosen_answer');
                }
                
                // Adicionar selected_option se não existir
                if (!Schema::hasColumn('user_answers', 'selected_option')) {
                    $table->char('selected_option', 1)->nullable()->after('question_id');
                }
            });
            
            // Sincronizar valores existentes
            DB::statement('UPDATE user_answers SET is_correct = correct WHERE is_correct != correct');
            DB::statement('UPDATE user_answers SET selected_option = chosen_answer WHERE selected_option != chosen_answer OR selected_option IS NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('user_answers', function (Blueprint $table) {
                $table->dropColumn(['is_correct', 'selected_option']);
            });
        }
    }
};
