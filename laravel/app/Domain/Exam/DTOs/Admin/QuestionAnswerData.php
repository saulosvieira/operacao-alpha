<?php

declare(strict_types=1);

namespace App\Domain\Exam\DTOs\Admin;

readonly class QuestionAnswerData
{
    public function __construct(
        public int $questionId,
        public int $questionNumber,
        public string $statement,
        public string $selectedOption,
        public string $correctAnswer,
        public bool $isCorrect,
        public string $optionA,
        public string $optionB,
        public string $optionC,
        public string $optionD,
        public string $optionE,
        public ?string $explanation,
        public float $accuracyRate,
        public bool $hasPendingComplaints,
    ) {}
}
