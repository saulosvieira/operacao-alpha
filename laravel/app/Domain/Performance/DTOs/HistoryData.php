<?php

namespace App\Domain\Performance\DTOs;

readonly class HistoryData
{
    public function __construct(
        public string $examId,
        public string $examTitle,
        public string $careerName,
        public float $score,
        public int $correctAnswers,
        public int $totalQuestions,
        public int $timeSpentMinutes,
        public string $completedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            examId: $data['exam_id'],
            examTitle: $data['exam_title'],
            careerName: $data['career_name'],
            score: $data['score'],
            correctAnswers: $data['correct_answers'],
            totalQuestions: $data['total_questions'],
            timeSpentMinutes: $data['time_spent_minutes'],
            completedAt: $data['completed_at'],
        );
    }

    public function toArray(): array
    {
        return [
            'exam_id' => $this->examId,
            'exam_title' => $this->examTitle,
            'career_name' => $this->careerName,
            'score' => $this->score,
            'correct_answers' => $this->correctAnswers,
            'total_questions' => $this->totalQuestions,
            'time_spent_minutes' => $this->timeSpentMinutes,
            'completed_at' => $this->completedAt,
        ];
    }
}
