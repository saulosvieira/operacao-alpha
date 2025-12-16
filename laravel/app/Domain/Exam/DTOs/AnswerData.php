<?php

namespace App\Domain\Exam\DTOs;

readonly class AnswerData
{
    public function __construct(
        public string $id,
        public string $attemptId,
        public string $questionId,
        public string $chosenAnswer,
        public bool $correct,
        public int $timeSeconds,
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            attemptId: $data['attempt_id'],
            questionId: $data['question_id'],
            chosenAnswer: $data['chosen_answer'],
            correct: $data['correct'],
            timeSeconds: $data['time_seconds'],
        );
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'attemptId' => $this->attemptId,
            'questionId' => $this->questionId,
            'chosenAnswer' => $this->chosenAnswer,
            'correct' => $this->correct,
            'timeSeconds' => $this->timeSeconds,
        ];
    }
}
