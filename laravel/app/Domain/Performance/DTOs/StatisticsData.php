<?php

namespace App\Domain\Performance\DTOs;

readonly class StatisticsData
{
    public function __construct(
        public int $totalExamsCompleted,
        public float $averageScore,
        public int $totalCorrectAnswers,
        public int $totalQuestions,
        public float $accuracyPercentage,
        public int $totalTimeSpentMinutes,
        public ?string $strongestCareer,
        public ?string $weakestCareer,
        public array $careerBreakdown,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalExamsCompleted: $data['total_exams_completed'] ?? 0,
            averageScore: $data['average_score'] ?? 0.0,
            totalCorrectAnswers: $data['total_correct_answers'] ?? 0,
            totalQuestions: $data['total_questions'] ?? 0,
            accuracyPercentage: $data['accuracy_percentage'] ?? 0.0,
            totalTimeSpentMinutes: $data['total_time_spent_minutes'] ?? 0,
            strongestCareer: $data['strongest_career'] ?? null,
            weakestCareer: $data['weakest_career'] ?? null,
            careerBreakdown: $data['career_breakdown'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'total_exams_completed' => $this->totalExamsCompleted,
            'average_score' => $this->averageScore,
            'total_correct_answers' => $this->totalCorrectAnswers,
            'total_questions' => $this->totalQuestions,
            'accuracy_percentage' => $this->accuracyPercentage,
            'total_time_spent_minutes' => $this->totalTimeSpentMinutes,
            'strongest_career' => $this->strongestCareer,
            'weakest_career' => $this->weakestCareer,
            'career_breakdown' => $this->careerBreakdown,
        ];
    }
}
