<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Resources\Json\JsonResource;

class AttemptResource extends JsonResource
{
    public function toArray($request): array
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
