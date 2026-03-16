<?php

declare(strict_types=1);

namespace App\Domain\Exam\DTOs\Admin;

readonly class AttemptDetailData
{
    public function __construct(
        public int $id,
        public string $userName,
        public string $userEmail,
        public string $examTitle,
        public string $careerName,
        public float $score,
        public int $correctAnswers,
        public int $totalQuestions,
        public int $durationMinutes,
        public string $finishedAt,
        public int $examId,
    ) {}
}
