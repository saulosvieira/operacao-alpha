<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\AttemptRepository;
use App\Domain\Exam\Repositories\ResultRepository;
use App\Domain\Exam\DTOs\ResultData;

class FinishAttemptAction
{
    public function __construct(
        private AttemptRepository $attemptRepository,
        private ResultRepository $resultRepository,
        private CalculateResultAction $calculateResult,
    ) {}
    
    public function execute(string $attemptId): ResultData
    {
        $attempt = $this->attemptRepository->findById($attemptId);
        
        if (!$attempt) {
            throw new \Exception('Attempt not found');
        }
        
        if ($attempt->finishedAt) {
            throw new \Exception('Attempt already finished');
        }
        
        $result = $this->calculateResult->execute($attemptId);
        
        // Calcular duração em segundos
        $startedAt = new \DateTime($attempt->startedAt);
        $finishedAt = new \DateTime();
        $durationSeconds = $finishedAt->getTimestamp() - $startedAt->getTimestamp();
        
        // Atualizar attempt
        $this->attemptRepository->update($attemptId, [
            'finished_at' => $finishedAt,
            'duration_seconds' => $durationSeconds,
            'correct_answers' => $result->totalCorrect,
            'score' => $result->finalScore,
        ]);
        
        // Criar resultado
        $this->resultRepository->create([
            'attempt_id' => $attemptId,
            'user_id' => $attempt->userId,
            'exam_id' => $attempt->examId,
            'total_questions' => $result->totalQuestions,
            'correct_answers' => $result->totalCorrect,
            'score' => $result->finalScore,
            'total_time_seconds' => $durationSeconds,
            'finished_at' => $finishedAt,
        ]);
        
        // Return result with actual duration
        return new ResultData(
            totalQuestions: $result->totalQuestions,
            totalCorrect: $result->totalCorrect,
            finalScore: $result->finalScore,
            totalTimeSeconds: $durationSeconds,
        );
    }
}
