<?php

declare(strict_types=1);

namespace App\Domain\Exam\DTOs\Admin;

readonly class ExamStatisticsData
{
    public function __construct(
        public int $totalAttempts,
        public float $avgScore,
        public float $avgAccuracy,
        public float $avgDurationMinutes,
    ) {}
}
