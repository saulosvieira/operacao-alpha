<?php

namespace App\Domain\Exam\DTOs;

use App\Domain\Exam\Models\Exam;

readonly class StartAttemptResultData
{
    public function __construct(
        public AttemptData $attempt,
        public Exam $exam,
        public int $initialTimerSeconds,
    ) {}
}
