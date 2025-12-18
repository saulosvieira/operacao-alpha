<?php

namespace App\Http\Resources\Exam;

use App\Domain\Exam\DTOs\StartAttemptResultData;
use Illuminate\Http\Resources\Json\JsonResource;

class StartAttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var StartAttemptResultData $result */
        $result = $this->resource;
        
        $feedbackMode = $result->exam->feedback_mode;
        $feedbackModeValue = $feedbackMode ? $feedbackMode->value : 'final';
        
        $attemptData = [
            'id' => $result->attempt->id,
            'userId' => $result->attempt->userId,
            'examId' => $result->attempt->examId,
            'startedAt' => $result->attempt->startedAt,
            'finishedAt' => $result->attempt->finishedAt,
            'durationSeconds' => $result->attempt->durationSeconds,
            'correctAnswers' => $result->attempt->correctAnswers,
            'score' => $result->attempt->score,
        ];
        
        // Include answers map if available (for resumed attempts)
        if ($result->attempt->answers !== null) {
            $attemptData['answers'] = $result->attempt->answers;
        }
        
        return [
            'attempt' => $attemptData,
            'exam' => [
                'id' => (string) $result->exam->id,
                'title' => $result->exam->title,
                'description' => $result->exam->description,
                'timeLimitMinutes' => $result->exam->time_limit_minutes,
                'feedbackMode' => $feedbackModeValue,
            ],
            'questions' => $this->getQuestionsWithoutAnswers($result->exam->questions),
            'initialTimerSeconds' => $result->initialTimerSeconds,
        ];
    }
    
    /**
     * Get questions without showing answers (for starting an attempt).
     *
     * @param \Illuminate\Database\Eloquent\Collection $questions
     * @return array
     */
    protected function getQuestionsWithoutAnswers($questions): array
    {
        return $questions->map(function ($question) {
            return (new QuestionResource($question))->showAnswer(false);
        })->toArray();
    }
}
