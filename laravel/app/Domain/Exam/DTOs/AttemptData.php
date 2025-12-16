<?php

namespace App\Domain\Exam\DTOs;

readonly class AttemptData
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $examId,
        public string $startedAt,
        public ?string $finishedAt,
        public ?int $durationSeconds,
        public ?int $correctAnswers,
        public ?float $score,
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['user_id'],
            examId: $data['exam_id'],
            startedAt: $data['started_at'],
            finishedAt: $data['finished_at'] ?? null,
            durationSeconds: $data['duration_seconds'] ?? null,
            correctAnswers: $data['correct_answers'] ?? null,
            score: $data['score'] ?? null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'examId' => $this->examId,
            'startedAt' => $this->startedAt,
            'finishedAt' => $this->finishedAt,
            'durationSeconds' => $this->durationSeconds,
            'correctAnswers' => $this->correctAnswers,
            'score' => $this->score,
        ];
    }
}
