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
        // 1. Rename carreiras → careers
        Schema::rename('carreiras', 'careers');
        Schema::table('careers', function (Blueprint $table) {
            $table->renameColumn('nome', 'name');
            $table->renameColumn('descricao', 'description');
            $table->renameColumn('ativa', 'active');
        });

        // 2. Rename editais → notices
        Schema::rename('editais', 'notices');
        Schema::table('notices', function (Blueprint $table) {
            $table->renameColumn('carreira_id', 'career_id');
            $table->renameColumn('titulo', 'title');
            $table->renameColumn('descricao', 'description');
            $table->renameColumn('data_publicacao', 'publication_date');
            $table->renameColumn('ativo', 'active');
        });

        // 3. Rename simulados → exams
        Schema::rename('simulados', 'exams');
        Schema::table('exams', function (Blueprint $table) {
            $table->renameColumn('carreira_id', 'career_id');
            $table->renameColumn('titulo', 'title');
            $table->renameColumn('descricao', 'description');
            $table->renameColumn('tempo_limite_minutos', 'time_limit_minutes');
            $table->renameColumn('ativo', 'active');
        });

        // 4. Rename questoes → questions
        Schema::rename('questoes', 'questions');
        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('simulado_id', 'exam_id');
            $table->renameColumn('numero_questao', 'question_number');
            $table->renameColumn('enunciado', 'statement');
            $table->renameColumn('imagem_enunciado', 'statement_image');
            $table->renameColumn('alternativa_a', 'option_a');
            $table->renameColumn('imagem_a', 'option_a_image');
            $table->renameColumn('alternativa_b', 'option_b');
            $table->renameColumn('imagem_b', 'option_b_image');
            $table->renameColumn('alternativa_c', 'option_c');
            $table->renameColumn('imagem_c', 'option_c_image');
            $table->renameColumn('alternativa_d', 'option_d');
            $table->renameColumn('imagem_d', 'option_d_image');
            $table->renameColumn('alternativa_e', 'option_e');
            $table->renameColumn('imagem_e', 'option_e_image');
            $table->renameColumn('resposta_correta', 'correct_answer');
            $table->renameColumn('explicacao', 'explanation');
        });

        // 5. Rename respostas_usuarios → user_answers
        Schema::rename('respostas_usuarios', 'user_answers');
        Schema::table('user_answers', function (Blueprint $table) {
            $table->renameColumn('simulado_id', 'exam_id');
            $table->renameColumn('questao_id', 'question_id');
            $table->renameColumn('resposta_escolhida', 'chosen_answer');
            $table->renameColumn('correta', 'correct');
            $table->renameColumn('tempo_resposta_segundos', 'time_seconds');
        });

        // 6. Rename resultados_simulados → exam_results
        Schema::rename('resultados_simulados', 'exam_results');
        Schema::table('exam_results', function (Blueprint $table) {
            $table->renameColumn('simulado_id', 'exam_id');
            $table->renameColumn('pontuacao', 'score');
            $table->renameColumn('total_questoes', 'total_questions');
            $table->renameColumn('acertos', 'correct_answers');
            $table->renameColumn('tempo_total_segundos', 'total_time_seconds');
            $table->renameColumn('finalizado_em', 'finished_at');
        });

        // 7. Update rankings table
        Schema::table('rankings', function (Blueprint $table) {
            $table->renameColumn('pontuacao_diaria', 'daily_score');
            $table->renameColumn('pontuacao_semanal', 'weekly_score');
            $table->renameColumn('data_calculo', 'calculated_at');
        });

        // 8. Rename aprovados → approved
        Schema::rename('aprovados', 'approved');
        Schema::table('approved', function (Blueprint $table) {
            $table->renameColumn('carreira_id', 'career_id');
            $table->renameColumn('edital_id', 'notice_id');
            $table->renameColumn('nome', 'name');
            $table->renameColumn('posicao', 'position');
            $table->renameColumn('ano', 'year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse in opposite order

        // 8. Reverse approved → aprovados
        Schema::table('approved', function (Blueprint $table) {
            $table->renameColumn('career_id', 'carreira_id');
            $table->renameColumn('notice_id', 'edital_id');
            $table->renameColumn('name', 'nome');
            $table->renameColumn('position', 'posicao');
            $table->renameColumn('year', 'ano');
        });
        Schema::rename('approved', 'aprovados');

        // 7. Reverse rankings
        Schema::table('rankings', function (Blueprint $table) {
            $table->renameColumn('daily_score', 'pontuacao_diaria');
            $table->renameColumn('weekly_score', 'pontuacao_semanal');
            $table->renameColumn('calculated_at', 'data_calculo');
        });

        // 6. Reverse exam_results → resultados_simulados
        Schema::table('exam_results', function (Blueprint $table) {
            $table->renameColumn('exam_id', 'simulado_id');
            $table->renameColumn('score', 'pontuacao');
            $table->renameColumn('total_questions', 'total_questoes');
            $table->renameColumn('correct_answers', 'acertos');
            $table->renameColumn('total_time_seconds', 'tempo_total_segundos');
            $table->renameColumn('finished_at', 'finalizado_em');
        });
        Schema::rename('exam_results', 'resultados_simulados');

        // 5. Reverse user_answers → respostas_usuarios
        Schema::table('user_answers', function (Blueprint $table) {
            $table->renameColumn('exam_id', 'simulado_id');
            $table->renameColumn('question_id', 'questao_id');
            $table->renameColumn('chosen_answer', 'resposta_escolhida');
            $table->renameColumn('correct', 'correta');
            $table->renameColumn('time_seconds', 'tempo_resposta_segundos');
        });
        Schema::rename('user_answers', 'respostas_usuarios');

        // 4. Reverse questions → questoes
        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('exam_id', 'simulado_id');
            $table->renameColumn('question_number', 'numero_questao');
            $table->renameColumn('statement', 'enunciado');
            $table->renameColumn('statement_image', 'imagem_enunciado');
            $table->renameColumn('option_a', 'alternativa_a');
            $table->renameColumn('option_a_image', 'imagem_a');
            $table->renameColumn('option_b', 'alternativa_b');
            $table->renameColumn('option_b_image', 'imagem_b');
            $table->renameColumn('option_c', 'alternativa_c');
            $table->renameColumn('option_c_image', 'imagem_c');
            $table->renameColumn('option_d', 'alternativa_d');
            $table->renameColumn('option_d_image', 'imagem_d');
            $table->renameColumn('option_e', 'alternativa_e');
            $table->renameColumn('option_e_image', 'imagem_e');
            $table->renameColumn('correct_answer', 'resposta_correta');
            $table->renameColumn('explanation', 'explicacao');
        });
        Schema::rename('questions', 'questoes');

        // 3. Reverse exams → simulados
        Schema::table('exams', function (Blueprint $table) {
            $table->renameColumn('career_id', 'carreira_id');
            $table->renameColumn('title', 'titulo');
            $table->renameColumn('description', 'descricao');
            $table->renameColumn('time_limit_minutes', 'tempo_limite_minutos');
            $table->renameColumn('active', 'ativo');
        });
        Schema::rename('exams', 'simulados');

        // 2. Reverse notices → editais
        Schema::table('notices', function (Blueprint $table) {
            $table->renameColumn('career_id', 'carreira_id');
            $table->renameColumn('title', 'titulo');
            $table->renameColumn('description', 'descricao');
            $table->renameColumn('publication_date', 'data_publicacao');
            $table->renameColumn('active', 'ativo');
        });
        Schema::rename('notices', 'editais');

        // 1. Reverse careers → carreiras
        Schema::table('careers', function (Blueprint $table) {
            $table->renameColumn('name', 'nome');
            $table->renameColumn('description', 'descricao');
            $table->renameColumn('active', 'ativa');
        });
        Schema::rename('careers', 'carreiras');
    }
};
