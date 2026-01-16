<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('correct_answers')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('exam_id');
            $table->index(['user_id', 'exam_id']);
            $table->index('started_at');
        });

        // Update user_answers to reference attempts instead of exams directly
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table due to foreign key constraints
            Schema::create('user_answers_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('attempt_id')->constrained('attempts')->onDelete('cascade');
                $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
                $table->string('selected_option', 1);
                $table->boolean('is_correct')->default(false);
                $table->timestamps();
                
                $table->index('attempt_id');
                $table->unique(['attempt_id', 'question_id']);
            });
            
            // Copy data if user_answers table exists and has data
            if (Schema::hasTable('user_answers')) {
                // We can't migrate existing data without attempts, so we'll just drop the old table
                Schema::drop('user_answers');
            }
            
            Schema::rename('user_answers_new', 'user_answers');
        } else {
            Schema::table('user_answers', function (Blueprint $table) {
                // Drop the old exam_id foreign key (using the original table name)
                $table->dropForeign('respostas_usuarios_simulado_id_foreign');
                $table->dropIndex('respostas_usuarios_user_id_simulado_id_index');
                $table->dropUnique('respostas_usuarios_user_id_simulado_id_questao_id_unique');
                
                // Add attempt_id
                $table->foreignId('attempt_id')->after('user_id')->constrained('attempts')->onDelete('cascade');
                
                // Drop exam_id column
                $table->dropColumn('exam_id');
                
                // Add new indexes
                $table->index('attempt_id');
                $table->unique(['attempt_id', 'question_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, recreate the original user_answers table
            Schema::create('user_answers_old', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
                $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
                $table->string('selected_option', 1);
                $table->boolean('is_correct')->default(false);
                $table->timestamps();
                
                $table->index(['user_id', 'exam_id']);
                $table->unique(['user_id', 'exam_id', 'question_id']);
            });
            
            Schema::drop('user_answers');
            Schema::rename('user_answers_old', 'user_answers');
        } else {
            // Restore user_answers structure
            Schema::table('user_answers', function (Blueprint $table) {
                $table->dropForeign(['attempt_id']);
                $table->dropIndex(['attempt_id']);
                $table->dropUnique(['attempt_id', 'question_id']);
                
                $table->dropColumn('attempt_id');
                $table->foreignId('exam_id')->after('user_id')->constrained('exams')->onDelete('cascade');
                
                $table->index(['user_id', 'exam_id']);
                $table->unique(['user_id', 'exam_id', 'question_id']);
            });
        }

        Schema::dropIfExists('attempts');
    }
};
