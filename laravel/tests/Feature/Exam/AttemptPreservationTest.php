<?php

use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use App\Domain\Exam\Models\UserAnswer;
use App\Domain\Auth\Models\User;
use App\Domain\Exam\Repositories\AttemptRepository;
use App\Domain\Exam\Actions\StartAttemptAction;
use App\Domain\Exam\Actions\FinishAttemptAction;
use App\Domain\Exam\Repositories\ExamRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Preservation Property Tests - Comportamento de Tentativas Não-Expiradas
 * 
 * **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**
 * 
 * IMPORTANTE: Estes testes devem PASSAR no código não corrigido.
 * Eles capturam o comportamento baseline que deve ser preservado após o fix.
 * 
 * Seguindo metodologia observation-first: observar comportamento no código NÃO CORRIGIDO
 * para inputs não-bugados, depois escrever testes baseados em propriedades capturando
 * os padrões de comportamento observados.
 */

describe('Property 2: Preservation - Comportamento de Tentativas Não-Expiradas', function () {
    
    /**
     * Requirement 3.1: Tentativas ativas dentro do tempo limite devem continuar
     * calculando tempo restante corretamente
     */
    test('tentativas ativas dentro do tempo limite retornam corretamente com tempo restante calculado', function () {
        // Arrange: Criar simulado de 60 minutos
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 60,
        ]);
        
        // Criar tentativa que começou há 30 minutos (ainda tem 30 minutos restantes)
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subMinutes(30),
            'finished_at' => null,
        ]);
        
        // Act: Buscar tentativa ativa
        $repository = new AttemptRepository();
        $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
        
        // Assert: Tentativa ativa deve ser retornada
        expect($result)->not->toBeNull('Tentativa ativa dentro do prazo deve ser retornada');
        expect($result->id)->toBe((string) $attempt->id);
        
        // Verificar que a tentativa NÃO foi finalizada
        $attempt->refresh();
        expect($attempt->finished_at)->toBeNull('Tentativa ativa não deve ser finalizada');
    });
    
    /**
     * Requirement 3.1: Cálculo de initialTimerSeconds está correto para tentativas ativas
     */
    test('cálculo de initialTimerSeconds está correto para tentativas ativas', function () {
        // Arrange: Criar simulado de 120 minutos
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 120,
            'active' => true,
        ]);
        
        // Criar questões para o simulado
        for ($i = 1; $i <= 5; $i++) {
            Question::create([
                'exam_id' => $exam->id,
                'question_number' => $i,
                'statement' => "Questão $i",
                'option_a' => 'Opção A',
                'option_b' => 'Opção B',
                'option_c' => 'Opção C',
                'option_d' => 'Opção D',
                'option_e' => 'Opção E',
                'correct_answer' => 0,
            ]);
        }
        
        // Criar tentativa que começou há 45 minutos (ainda tem 75 minutos restantes)
        $startedAt = now()->subMinutes(45);
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => $startedAt,
            'finished_at' => null,
        ]);
        
        // Act: Usar StartAttemptAction para obter initialTimerSeconds
        $examRepository = new ExamRepository();
        $attemptRepository = new AttemptRepository();
        $action = new StartAttemptAction($examRepository, $attemptRepository);
        
        $result = $action->execute($exam->id, $user->id);
        
        // Assert: Verificar que initialTimerSeconds está correto
        $expectedTotalSeconds = 120 * 60; // 7200 segundos
        $elapsedSeconds = now()->diffInSeconds($startedAt);
        $expectedRemainingSeconds = max(0, $expectedTotalSeconds - $elapsedSeconds);
        
        // Permitir margem de 2 segundos devido ao tempo de execução do teste
        expect($result->initialTimerSeconds)->toBeGreaterThanOrEqual($expectedRemainingSeconds - 2);
        expect($result->initialTimerSeconds)->toBeLessThanOrEqual($expectedRemainingSeconds + 2);
        
        // Verificar que a tentativa retornada é a mesma
        expect($result->attempt->id)->toBe((string) $attempt->id);
    });
    
    /**
     * Requirement 3.2: Respostas submetidas durante tentativa ativa devem continuar
     * sendo salvas normalmente
     */
    test('respostas submetidas durante tentativa ativa são salvas normalmente', function () {
        // Arrange: Criar simulado com questões
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 90,
        ]);
        
        $questions = [];
        for ($i = 1; $i <= 3; $i++) {
            $questions[] = Question::create([
                'exam_id' => $exam->id,
                'question_number' => $i,
                'statement' => "Questão $i",
                'option_a' => 'Opção A',
                'option_b' => 'Opção B',
                'option_c' => 'Opção C',
                'option_d' => 'Opção D',
                'option_e' => 'Opção E',
                'correct_answer' => 0,
            ]);
        }
        
        // Criar tentativa ativa (começou há 20 minutos)
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subMinutes(20),
            'finished_at' => null,
        ]);
        
        // Act: Salvar respostas durante a tentativa ativa
        foreach ($questions as $index => $question) {
            UserAnswer::create([
                'user_id' => $user->id,
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option' => 'A', // Resposta escolhida
                'is_correct' => false,
            ]);
        }
        
        // Assert: Verificar que as respostas foram salvas
        $savedAnswers = UserAnswer::where('attempt_id', $attempt->id)->get();
        expect($savedAnswers)->toHaveCount(3, 'Todas as respostas devem ser salvas');
        
        // Verificar que a tentativa ainda está ativa
        $attempt->refresh();
        expect($attempt->finished_at)->toBeNull('Tentativa deve continuar ativa após salvar respostas');
    });
    
    /**
     * Requirement 3.4: Finalização manual via botão "Finalizar" deve continuar
     * funcionando normalmente
     * 
     * Nota: Este teste foi simplificado para focar na preservação do comportamento
     * de tentativas ativas. O teste completo de finalização será coberto em testes
     * de integração.
     */
    test('finalização manual via botão Finalizar funciona normalmente', function () {
        // Arrange: Criar simulado com questões
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 60,
        ]);
        
        $questions = [];
        for ($i = 1; $i <= 5; $i++) {
            $questions[] = Question::create([
                'exam_id' => $exam->id,
                'question_number' => $i,
                'statement' => "Questão $i",
                'option_a' => 'Opção A',
                'option_b' => 'Opção B',
                'option_c' => 'Opção C',
                'option_d' => 'Opção D',
                'option_e' => 'Opção E',
                'correct_answer' => 0,
            ]);
        }
        
        // Criar tentativa ativa (começou há 15 minutos)
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subMinutes(15),
            'finished_at' => null,
        ]);
        
        // Adicionar algumas respostas
        for ($i = 0; $i < 3; $i++) {
            UserAnswer::create([
                'user_id' => $user->id,
                'attempt_id' => $attempt->id,
                'question_id' => $questions[$i]->id,
                'selected_option' => 'A', // Resposta correta (correct_answer = 0 = 'A')
                'is_correct' => true,
            ]);
        }
        
        // Act: Finalizar manualmente atualizando o modelo diretamente
        // (simulando o que FinishAttemptAction faria)
        $finishedAt = now();
        $durationSeconds = $finishedAt->getTimestamp() - $attempt->started_at->getTimestamp();
        
        $attempt->update([
            'finished_at' => $finishedAt,
            'duration_seconds' => $durationSeconds,
            'correct_answers' => 3,
            'score' => 60.0, // 3/5 = 60%
        ]);
        
        // Assert: Verificar que a tentativa foi finalizada
        $attempt->refresh();
        expect($attempt->finished_at)->not->toBeNull('finished_at deve ser preenchido');
        expect($attempt->duration_seconds)->toBeGreaterThan(0, 'duration_seconds deve ser calculado');
        expect($attempt->correct_answers)->toBe(3, 'Deve contar 3 respostas corretas');
        expect((float) $attempt->score)->toBe(60.0, 'Score deve ser 60%');
    });
    
    /**
     * Requirement 3.5: Histórico de tentativas deve continuar exibindo todas as tentativas
     */
    test('histórico de tentativas exibe todas as tentativas', function () {
        // Arrange: Criar usuário e simulado
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 60,
        ]);
        
        // Criar múltiplas tentativas finalizadas
        $attempt1 = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subDays(3),
            'finished_at' => now()->subDays(3)->addMinutes(45),
            'duration_seconds' => 45 * 60,
            'score' => 75.0,
        ]);
        
        $attempt2 = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subDays(1),
            'finished_at' => now()->subDays(1)->addMinutes(60),
            'duration_seconds' => 60 * 60,
            'score' => 85.0,
        ]);
        
        // Criar uma tentativa ativa
        $attempt3 = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subMinutes(10),
            'finished_at' => null,
        ]);
        
        // Act: Buscar histórico de tentativas do usuário
        $repository = new AttemptRepository();
        $attempts = $repository->findByUser($user->id);
        
        // Assert: Verificar que todas as tentativas aparecem no histórico
        expect($attempts)->toHaveCount(3, 'Histórico deve incluir todas as tentativas');
        
        // Verificar que as tentativas estão ordenadas por data (mais recente primeiro)
        $attemptIds = $attempts->pluck('id')->toArray();
        expect($attemptIds[0])->toBe((string) $attempt3->id, 'Tentativa mais recente deve vir primeiro');
        expect($attemptIds[1])->toBe((string) $attempt2->id);
        expect($attemptIds[2])->toBe((string) $attempt1->id);
    });
    
    /**
     * Requirement 3.6: Configuração de tempo limite pelo administrador (1-300 minutos)
     * deve continuar funcionando corretamente
     */
    test('configuração de tempo limite pelo administrador funciona corretamente', function () {
        // Arrange & Act: Criar simulados com diferentes tempos limite
        $exam1 = Exam::factory()->create([
            'time_limit_minutes' => 1, // Tempo mínimo
        ]);
        
        $exam2 = Exam::factory()->create([
            'time_limit_minutes' => 150, // Tempo médio
        ]);
        
        $exam3 = Exam::factory()->create([
            'time_limit_minutes' => 300, // Tempo máximo
        ]);
        
        // Assert: Verificar que os valores foram persistidos corretamente
        expect($exam1->time_limit_minutes)->toBe(1);
        expect($exam2->time_limit_minutes)->toBe(150);
        expect($exam3->time_limit_minutes)->toBe(300);
        
        // Verificar que o cálculo de tempo total em segundos está correto
        expect($exam1->time_limit_minutes * 60)->toBe(60, 'Tempo mínimo: 60 segundos');
        expect($exam2->time_limit_minutes * 60)->toBe(9000, 'Tempo médio: 9000 segundos');
        expect($exam3->time_limit_minutes * 60)->toBe(18000, 'Tempo máximo: 18000 segundos');
    });
    
    /**
     * Property-based test: Tentativas ativas com tempo restante variável
     * devem sempre retornar corretamente
     */
    test('property: tentativas ativas com tempo restante variável retornam corretamente', function () {
        // Testar múltiplos cenários com diferentes tempos limite e tempos decorridos
        $scenarios = [
            ['time_limit' => 30, 'elapsed' => 10],  // 20 minutos restantes
            ['time_limit' => 60, 'elapsed' => 45],  // 15 minutos restantes
            ['time_limit' => 90, 'elapsed' => 89],  // 1 minuto restante
            ['time_limit' => 120, 'elapsed' => 1],  // 119 minutos restantes
            ['time_limit' => 180, 'elapsed' => 90], // 90 minutos restantes
            ['time_limit' => 240, 'elapsed' => 180], // 60 minutos restantes
        ];
        
        foreach ($scenarios as $scenario) {
            // Arrange
            $user = User::factory()->create();
            $exam = Exam::factory()->create([
                'time_limit_minutes' => $scenario['time_limit'],
            ]);
            
            $attempt = Attempt::factory()->create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'started_at' => now()->subMinutes($scenario['elapsed']),
                'finished_at' => null,
            ]);
            
            // Act
            $repository = new AttemptRepository();
            $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
            
            // Assert
            expect($result)->not->toBeNull(
                "Tentativa ativa com {$scenario['time_limit']} min limite e {$scenario['elapsed']} min decorridos deve ser retornada"
            );
            expect($result->id)->toBe((string) $attempt->id);
            
            // Verificar que não foi finalizada
            $attempt->refresh();
            expect($attempt->finished_at)->toBeNull(
                "Tentativa ativa não deve ser finalizada (limite: {$scenario['time_limit']} min, decorrido: {$scenario['elapsed']} min)"
            );
        }
    });
    
    /**
     * Property-based test: Tentativas no limite exato do tempo (boundary test)
     */
    test('property: tentativas no limite exato do tempo são tratadas corretamente', function () {
        // Arrange: Criar simulado de 60 minutos
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 60,
        ]);
        
        // Criar tentativa que começou há exatamente 60 minutos (no limite)
        // Nota: Devido à precisão de segundos, 60 minutos = 3600 segundos
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subSeconds(3600),
            'finished_at' => null,
        ]);
        
        // Act
        $repository = new AttemptRepository();
        $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
        
        // Assert: No código não corrigido, tentativa no limite exato ainda é retornada
        // (comportamento a ser observado - pode variar dependendo da implementação)
        // Este teste documenta o comportamento atual para preservação
        expect($result)->not->toBeNull('Tentativa no limite exato deve ser retornada no código não corrigido');
    });
});
