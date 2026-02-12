<?php

namespace App\Http\Resources\Performance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Domain\Performance\DTOs\StatisticsData;

class StatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var StatisticsData $this->resource */
        return [
            'total_exams_completed' => $this->resource->totalExamsCompleted,
            'average_score' => $this->resource->averageScore,
            'total_correct_answers' => $this->resource->totalCorrectAnswers,
            'total_questions' => $this->resource->totalQuestions,
            'accuracy_percentage' => $this->resource->accuracyPercentage,
            'total_time_spent_minutes' => $this->resource->totalTimeSpentMinutes,
            'strongest_career' => $this->resource->strongestCareer,
            'weakest_career' => $this->resource->weakestCareer,
            'career_breakdown' => $this->resource->careerBreakdown,
        ];
    }
}
