<?php

namespace App\Domain\Exam\DTOs;

readonly class ResultData
{
    public function __construct(
        public int $totalQuestions,
        public int $totalCorrect,
        public float $finalScore,
        public ?int $totalTimeSeconds = null,
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            totalQuestions: $data['total_questions'],
            totalCorrect: $data['total_correct'],
            finalScore: $data['final_score'],
            totalTimeSeconds: $data['total_time_seconds'] ?? null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'totalQuestions' => $this->totalQuestions,
            'totalCorrect' => $this->totalCorrect,
            'finalScore' => $this->finalScore,
            'totalTimeSeconds' => $this->totalTimeSeconds,
        ];
    }
}
