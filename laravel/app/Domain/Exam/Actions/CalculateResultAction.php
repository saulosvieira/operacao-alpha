<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\AnswerRepository;
use App\Domain\Exam\Repositories\AttemptRepository;
use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\DTOs\ResultData;

class CalculateResultAction
{
    public function __construct(
        private AnswerRepository $answerRepository,
        private AttemptRepository $attemptRepository,
        private ExamRepository $examRepository,
    ) {}
    
    public function execute(string $attemptId): ResultData
    {
        $attempt = $this->attemptRepository->findById($attemptId);
        
        if (!$attempt) {
            throw new \Exception('Attempt not found');
        }
        
        $exam = $this->examRepository->findById($attempt->examId);
        
        if (!$exam) {
            throw new \Exception('Exam not found');
        }
        
        $answers = $this->answerRepository->findByAttempt($attemptId);
        
        // Use total questions from exam, not just answered questions
        $totalQuestions = $exam->totalQuestions;
        $totalCorrect = $answers->filter(fn($a) => $a->correct)->count();
        
        // Score is percentage of correct answers out of total questions
        $finalScore = $totalQuestions > 0 
            ? round(($totalCorrect / $totalQuestions) * 100, 2)
            : 0;
        
        $totalTimeSeconds = $answers->sum(fn($a) => $a->timeSeconds);
        
        return new ResultData(
            totalQuestions: $totalQuestions,
            totalCorrect: $totalCorrect,
            finalScore: $finalScore,
            totalTimeSeconds: $totalTimeSeconds,
        );
    }
}
