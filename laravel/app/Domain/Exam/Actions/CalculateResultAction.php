<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\AnswerRepository;
use App\Domain\Exam\DTOs\ResultData;

class CalculateResultAction
{
    public function __construct(
        private AnswerRepository $answerRepository,
    ) {}
    
    public function execute(string $attemptId): ResultData
    {
        $answers = $this->answerRepository->findByAttempt($attemptId);
        
        $totalQuestions = $answers->count();
        $totalCorrect = $answers->filter(fn($a) => $a->correct)->count();
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
