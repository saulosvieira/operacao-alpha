<?php

declare(strict_types=1);

namespace App\Domain\Exam\DTOs\Admin;

use Illuminate\Support\Carbon;

readonly class AttemptListItemData
{
    public function __construct(
        public int $id,
        public string $userName,
        public string $examTitle,
        public string $careerName,
        public float $score,
        public int $correctAnswers,
        public int $totalQuestions,
        public int $durationMinutes,
        public string|Carbon $finishedAt,
        public int $pendingComplaints,
    ) {}
}
