<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\AttemptRepository;
use App\Domain\Exam\DTOs\AttemptData;
use App\Domain\Exam\DTOs\StartAttemptResultData;

class StartAttemptAction
{
    public function __construct(
        private ExamRepository $examRepository,
        private AttemptRepository $attemptRepository,
    ) {}
    
    public function execute(string $examId, string $userId): StartAttemptResultData
    {
        // Load exam with questions
        $exam = $this->examRepository->findByIdWithQuestions($examId);
        
        if (!$exam) {
            throw new \Exception('Exam not found');
        }
        
        if (!$exam->active) {
            throw new \Exception('Exam is not active');
        }
        
        // Verificar se já existe uma tentativa ativa para este simulado específico
        $activeAttempt = $this->attemptRepository->findActiveByUserAndExam($userId, $examId);
        
        if ($activeAttempt) {
            // Calculate remaining time based on when attempt started
            $totalSeconds = $exam->time_limit_minutes * 60;
            $elapsedSeconds = now()->diffInSeconds($activeAttempt->startedAt);
            $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);
            
            // Se já existe uma tentativa ativa para este simulado, retornar ela ao invés de criar nova
            return new StartAttemptResultData(
                attempt: $activeAttempt,
                exam: $exam,
                initialTimerSeconds: $remainingSeconds,
            );
        }
        
        $attemptData = $this->attemptRepository->create([
            'exam_id' => $examId,
            'user_id' => $userId,
            'started_at' => now(),
        ]);
        
        // Return attempt with exam and questions data
        return new StartAttemptResultData(
            attempt: $attemptData,
            exam: $exam,
            initialTimerSeconds: $exam->time_limit_minutes * 60,
        );
    }
}
