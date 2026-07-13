<?php

namespace Database\Seeders;

use App\Domain\Career\Models\Career;
use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\ExamResult;
use App\Domain\Exam\Models\Question;
use App\Domain\Exam\Models\UserAnswer;
use App\Domain\Import\Imports\ExcelQuestionImport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Seeder para importar a primeira carga de simulados a partir dos arquivos Excel
 * localizados na pasta simulados/ na raiz do projeto.
 *
 * Estrutura esperada dos Excel:
 * - Colunas: carreira, materia, assunto, enunciado, alternativa_a-e, correta, comentario,
 *   nivel_dificuldade, texto_apoio, link_pdf_apoio, ano, banca
 * - Arquivos: {Matéria} {N}.xlsx (ex: "Portugues 1.xlsx", "Direito Penal 3.xlsx")
 *
 * Os simulados são organizados por matéria e vinculados a TODAS as carreiras ativas,
 * pois são simulados de matérias comuns a todos os concursos militares.
 */
class SimuladosSeeder extends Seeder
{
    /**
     * Mapeamento de matérias (nome no arquivo => dados para o exam).
     */
    private array $materias = [
        'Portugues' => [
            'title_prefix' => 'Português',
            'description' => 'Simulado de Língua Portuguesa com questões de gramática, interpretação de texto e redação.',
        ],
        'Matematica' => [
            'title_prefix' => 'Matemática',
            'description' => 'Simulado de Matemática com questões de raciocínio lógico, aritmética, álgebra e geometria.',
        ],
        'Direito Constitucional' => [
            'title_prefix' => 'Direito Constitucional',
            'description' => 'Simulado de Direito Constitucional com questões sobre a Constituição Federal, direitos fundamentais e organização do Estado.',
        ],
        'Direito Penal' => [
            'title_prefix' => 'Direito Penal',
            'description' => 'Simulado de Direito Penal com questões sobre crimes, penas, parte geral e especial do Código Penal.',
        ],
        'Informatica' => [
            'title_prefix' => 'Informática',
            'description' => 'Simulado de Informática com questões sobre sistemas operacionais, redes, segurança e ferramentas de escritório.',
        ],
    ];

    /**
     * Caminho base dos arquivos Excel (relativo à raiz do projeto).
     */
    private string $excelBasePath;

    public function __construct()
    {
        $this->excelBasePath = base_path('../simulados');
    }

    public function run(): void
    {
        $this->command->info('🎯 Iniciando importação dos simulados da primeira carga...');
        $this->command->newLine();

        // Limpar dados antigos (questions, attempts, results, user_answers, e exams)
        $this->command->info('🗑️  Limpando simulados antigos...');
        $examIds = Exam::pluck('id');

        if ($examIds->isNotEmpty()) {
            // Buscar attempt_ids e question_ids vinculados aos exams
            $attemptIds = Attempt::whereIn('exam_id', $examIds)->pluck('id');
            $questionIds = Question::whereIn('exam_id', $examIds)->pluck('id');

            // Deletar na ordem correta para respeitar foreign keys
            if ($attemptIds->isNotEmpty()) {
                UserAnswer::whereIn('attempt_id', $attemptIds)->delete();
            }
            ExamResult::whereIn('exam_id', $examIds)->delete();
            Attempt::whereIn('exam_id', $examIds)->delete();
            Question::whereIn('exam_id', $examIds)->delete();
            Exam::whereIn('id', $examIds)->delete();

            $this->command->info("   ✅ {$examIds->count()} simulados antigos removidos com questões e dados relacionados.");
        } else {
            $this->command->info('   ℹ️  Nenhum simulado antigo encontrado.');
        }

        $this->command->newLine();

        // Verificar se a pasta simulados existe
        if (!is_dir($this->excelBasePath)) {
            $this->command->error("Pasta de simulados não encontrada: {$this->excelBasePath}");
            $this->command->info('A pasta simulados/ deve existir na raiz do projeto com os arquivos Excel.');
            return;
        }

        // Buscar todas as carreiras ativas
        $careers = Career::where('active', true)->get();

        if ($careers->isEmpty()) {
            $this->command->error('Nenhuma carreira ativa encontrada. Execute o CarreiraSeeder primeiro.');
            return;
        }

        $this->command->info("📋 Carreiras encontradas: {$careers->count()}");
        foreach ($careers as $career) {
            $this->command->info("   - {$career->name}");
        }
        $this->command->newLine();

        $totalExams = 0;
        $totalQuestions = 0;
        $errors = [];

        // Para cada carreira, criar os exams e importar questões
        foreach ($careers as $career) {
            $this->command->info("🏢 Processando carreira: {$career->name}");

            foreach ($this->materias as $filePrefix => $materiaConfig) {
                for ($version = 1; $version <= 6; $version++) {
                    $filename = "{$filePrefix} {$version}.xlsx";
                    $filePath = "{$this->excelBasePath}/{$filename}";

                    if (!file_exists($filePath)) {
                        $this->command->warn("   ⚠️  Arquivo não encontrado: {$filename}");
                        continue;
                    }

                    // Criar o exam
                    $examTitle = "{$materiaConfig['title_prefix']} - Simulado {$version}";

                    $exam = Exam::create([
                        'career_id' => $career->id,
                        'title' => $examTitle,
                        'description' => $materiaConfig['description'],
                        'time_limit_minutes' => 60,
                        'active' => true,
                        'is_free' => $version <= 2, // Primeiros 2 de cada matéria são gratuitos
                        'feedback_mode' => 'final',
                    ]);

                    $totalExams++;

                    // Importar questões do Excel
                    $questionsImported = $this->importQuestionsFromExcel($exam, $filePath);

                    if ($questionsImported > 0) {
                        $totalQuestions += $questionsImported;
                        $this->command->info("   ✅ {$examTitle}: {$questionsImported} questões importadas");
                    } else {
                        $errors[] = "Nenhuma questão importada de {$filename} para {$career->name}";
                        $this->command->warn("   ❌ {$examTitle}: nenhuma questão importada");
                    }
                }
            }

            $this->command->newLine();
        }

        // Resumo final
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📊 RESUMO DA IMPORTAÇÃO');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info("   Carreiras processadas: {$careers->count()}");
        $this->command->info("   Simulados criados: {$totalExams}");
        $this->command->info("   Questões importadas: {$totalQuestions}");

        if (!empty($errors)) {
            $this->command->newLine();
            $this->command->warn("   ⚠️  Erros encontrados: " . count($errors));
            foreach ($errors as $error) {
                $this->command->warn("      - {$error}");
            }
        }

        $this->command->newLine();
        $this->command->info('✅ Importação concluída!');
    }

    /**
     * Importa questões de um arquivo Excel para um exam específico.
     */
    private function importQuestionsFromExcel(Exam $exam, string $filePath): int
    {
        try {
            $import = new ExcelQuestionImport();
            Excel::import($import, $filePath);

            $data = $import->getProcessedData();
            $questionNumber = 0;

            foreach ($data as $row) {
                // Verificar se a linha tem os campos mínimos necessários
                if (empty($row['statement']) || empty($row['correct_answer'])) {
                    continue;
                }

                // Verificar se tem pelo menos 2 alternativas preenchidas
                $options = array_filter([
                    $row['option_a'] ?? null,
                    $row['option_b'] ?? null,
                    $row['option_c'] ?? null,
                    $row['option_d'] ?? null,
                    $row['option_e'] ?? null,
                ]);

                if (count($options) < 2) {
                    continue;
                }

                $questionNumber++;

                // Validar URL do PDF de apoio
                $supportPdfUrl = $row['support_pdf_url'] ?? null;
                if ($supportPdfUrl && !filter_var($supportPdfUrl, FILTER_VALIDATE_URL)) {
                    $supportPdfUrl = null;
                }

                Question::create([
                    'exam_id' => $exam->id,
                    'question_number' => $questionNumber,
                    'statement' => $row['statement'],
                    'option_a' => $row['option_a'] ?? '',
                    'option_b' => $row['option_b'] ?? '',
                    'option_c' => $row['option_c'] ?? '',
                    'option_d' => $row['option_d'] ?? '',
                    'option_e' => $row['option_e'] ?? '',
                    'correct_answer' => strtoupper($row['correct_answer']),
                    'explanation' => $row['explanation'] ?? null,
                    'support_text' => $row['support_text'] ?? null,
                    'support_pdf_url' => $supportPdfUrl,
                    'year' => !empty($row['year']) ? (int) $row['year'] : null,
                    'exam_board' => $row['exam_board'] ?? null,
                ]);
            }

            return $questionNumber;
        } catch (\Exception $e) {
            Log::error("Erro ao importar questões do Excel: {$filePath}", [
                'error' => $e->getMessage(),
                'exam_id' => $exam->id,
            ]);

            return 0;
        }
    }
}
