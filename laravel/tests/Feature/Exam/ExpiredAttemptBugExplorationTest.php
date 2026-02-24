<?php

use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\Exam;
use App\Domain\Auth\Models\User;
use App\Domain\Exam\Repositories\AttemptRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Bug Exploration Test - Tentativas Expiradas Permanecem Ativas
 * 
 * **Validates: Requirements 1.1, 1.2, 1.3, 1.4**
 * 
 * CRÍTICO: Este teste DEVE FALHAR no código não corrigido - a falha confirma que o bug existe.
 * 
 * Este teste verifica que tentativas expiradas são retornadas como ativas pelo método
 * findActiveByUserAndExam (comportamento bugado). Quando o bug for corrigido, este teste
 * passará porque o método auto-finalizará tentativas expiradas e retornará NULL.
 */

describe('Property 1: Fault Condition - Tentativas Expiradas Permanecem Ativas', function () {
    
    test('tentativa expirada há 1 hora deve ser auto-finalizada', function () {
        // Arrange: Criar simulado de 60 minutos
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 60,
        ]);
        
        // Criar tentativa que começou há 2 horas (expirada há 1 hora)
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subHours(2),
            'finished_at' => null, // Tentativa ainda "ativa"
        ]);
        
        // Act: Buscar tentativa ativa
        $repository = new AttemptRepository();
        $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
        
        // Assert: Após o fix, deve retornar NULL (tentativa foi auto-finalizada)
        // No código bugado, retorna a tentativa expirada
        expect($result)->toBeNull('Tentativa expirada deve ser auto-finalizada e retornar NULL');
        
        // Verificar que a tentativa foi finalizada no banco
        $attempt->refresh();
        expect($attempt->finished_at)->not->toBeNull('finished_at deve ser preenchido');
        expect($attempt->duration_seconds)->toBe(60 * 60, 'duration_seconds deve ser o tempo limite completo');
        expect($attempt->score)->not->toBeNull('score deve ser calculado (resultado parcial)');
    });
    
    test('tentativa expirada há dias deve ser auto-finalizada', function () {
        // Arrange: Criar simulado de 120 minutos
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 120,
        ]);
        
        // Criar tentativa que começou há 5 dias (muito expirada)
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subDays(5),
            'finished_at' => null,
        ]);
        
        // Act
        $repository = new AttemptRepository();
        $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
        
        // Assert
        expect($result)->toBeNull('Tentativa expirada há dias deve ser auto-finalizada');
        
        $attempt->refresh();
        expect($attempt->finished_at)->not->toBeNull();
        expect($attempt->duration_seconds)->toBe(120 * 60);
    });
    
    test('tentativa expirada com respostas parciais deve ser auto-finalizada', function () {
        // Arrange: Criar simulado de 90 minutos
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 90,
        ]);
        
        // Criar tentativa que começou há 3 horas (expirada há 30 minutos)
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subHours(3),
            'finished_at' => null,
        ]);
        
        // Simular 10 respostas já submetidas
        // (Nota: não precisamos criar UserAnswer reais para este teste de exploração,
        // pois estamos focando na detecção de expiração)
        
        // Act
        $repository = new AttemptRepository();
        $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
        
        // Assert
        expect($result)->toBeNull('Tentativa expirada com respostas parciais deve ser auto-finalizada');
        
        $attempt->refresh();
        expect($attempt->finished_at)->not->toBeNull();
        expect($attempt->duration_seconds)->toBe(90 * 60);
        expect($attempt->score)->not->toBeNull('Score deve ser calculado com respostas parciais');
    });
    
    test('tentativa expirada por 1 segundo deve ser auto-finalizada', function () {
        // Arrange: Criar simulado de 1 minuto (tempo mínimo)
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'time_limit_minutes' => 1,
        ]);
        
        // Criar tentativa que começou há 61 segundos (expirada por 1 segundo)
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now()->subSeconds(61),
            'finished_at' => null,
        ]);
        
        // Act
        $repository = new AttemptRepository();
        $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
        
        // Assert
        expect($result)->toBeNull('Tentativa expirada por 1 segundo deve ser auto-finalizada');
        
        $attempt->refresh();
        expect($attempt->finished_at)->not->toBeNull();
        expect($attempt->duration_seconds)->toBe(60);
    });
    
    test('tentativa ativa dentro do prazo NÃO deve ser finalizada', function () {
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
        
        // Act
        $repository = new AttemptRepository();
        $result = $repository->findActiveByUserAndExam($user->id, $exam->id);
        
        // Assert: Tentativa ativa deve ser retornada normalmente
        expect($result)->not->toBeNull('Tentativa ativa dentro do prazo deve ser retornada');
        expect($result->id)->toBe((string) $attempt->id);
        
        // Verificar que a tentativa NÃO foi finalizada
        $attempt->refresh();
        expect($attempt->finished_at)->toBeNull('Tentativa ativa não deve ser finalizada');
    });
});
