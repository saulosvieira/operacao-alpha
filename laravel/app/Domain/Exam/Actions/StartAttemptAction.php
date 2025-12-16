<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\AttemptRepository;
use App\Domain\Exam\DTOs\AttemptData;

class StartAttemptAction
{
    public function __construct(
        private ExamRepository $examRepository,
        private AttemptRepository $attemptRepository,
    ) {}
    
    public function execute(string $examId, string $userId): AttemptData
    {
        $exam = $this->examRepository->findById($examId);
        
        if (!$exam) {
            throw new \Exception('Exam not found');
        }
        
        if (!$exam->active) {
            throw new \Exception('Exam is not active');
        }
        
        // Verificar se já existe uma tentativa ativa
        $activeAttempt = $this->attemptRepository->findActiveByUser($userId);
        
        if ($activeAttempt) {
            throw new \Exception('User already has an active attempt');
        }
        
        return $this->attemptRepository->create([
            'exam_id' => $examId,
            'user_id' => $userId,
            'started_at' => now(),
        ]);
    }
}
