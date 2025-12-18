<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Whether to include questions in the response.
     *
     * @var bool
     */
    protected $includeQuestions = false;

    /**
     * Whether to show answers in questions.
     *
     * @var bool
     */
    protected $showAnswers = false;

    /**
     * Set whether to include questions.
     *
     * @param bool $include
     * @return self
     */
    public function includeQuestions($include = true)
    {
        $this->includeQuestions = $include;
        return $this;
    }

    /**
     * Set whether to show answers in questions.
     *
     * @param bool $show
     * @return self
     */
    public function showAnswers($show = true)
    {
        $this->showAnswers = $show;
        return $this;
    }

    public function toArray($request): array
    {
        $resource = $this->resource;
        
        // Check if resource is a DTO or Eloquent Model
        $isDTO = $resource instanceof \App\Domain\Exam\DTOs\ExamData;
        
        if ($isDTO) {
            // Handle ExamData DTO
            $data = [
                'id' => (string) $resource->id,
                'careerId' => (string) $resource->careerId,
                'title' => $resource->title,
                'description' => $resource->description,
                'durationMin' => $resource->timeLimitMinutes,
                'numQuestions' => $resource->totalQuestions,
                'active' => $resource->active,
                'isFree' => $resource->isFree,
                'feedbackMode' => $resource->feedbackMode,
            ];
        } else {
            // Handle Eloquent Model
            $data = [
                'id' => (string) $resource->id,
                'careerId' => (string) $resource->career_id,
                'title' => $resource->title,
                'description' => $resource->description,
                'durationMin' => $resource->time_limit_minutes,
                'numQuestions' => $resource->questions()->count(),
                'active' => $resource->active,
                'isFree' => $resource->is_free ?? false,
                'feedbackMode' => $resource->feedback_mode ? $resource->feedback_mode->value : 'final',
            ];

            // Include questions if requested and loaded (only for Eloquent models)
            if ($this->includeQuestions && $resource->relationLoaded('questions')) {
                $data['questions'] = $resource->questions->map(function ($question) {
                    return (new QuestionResource($question))->showAnswer($this->showAnswers);
                });
            }
        }

        return $data;
    }
}
