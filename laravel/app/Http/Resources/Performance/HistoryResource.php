<?php

namespace App\Http\Resources\Performance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Domain\Performance\DTOs\HistoryData;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var HistoryData $this->resource */
        return [
            'exam_id' => $this->resource->examId,
            'exam_title' => $this->resource->examTitle,
            'career_name' => $this->resource->careerName,
            'score' => $this->resource->score,
            'correct_answers' => $this->resource->correctAnswers,
            'total_questions' => $this->resource->totalQuestions,
            'time_spent_minutes' => $this->resource->timeSpentMinutes,
            'completed_at' => $this->resource->completedAt,
        ];
    }
}
