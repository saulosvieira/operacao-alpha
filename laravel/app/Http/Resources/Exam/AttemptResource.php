<?php

namespace App\Http\Resources\Exam;

use App\Domain\Exam\Enums\FeedbackMode;
use Illuminate\Http\Resources\Json\JsonResource;

class AttemptResource extends JsonResource
{
    /**
     * Whether to include questions in the response.
     *
     * @var bool
     */
    protected $includeQuestions = false;

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

    public function toArray($request): array
    {
        $resource = $this->resource;
        $isDTO = $resource instanceof \App\Domain\Exam\DTOs\AttemptData;
        
        if ($isDTO) {
            // Handle AttemptData DTO (camelCase properties)
            $data = [
                'id' => (string) $resource->id,
                'userId' => (string) $resource->userId,
                'examId' => (string) $resource->examId,
                'startedAt' => $resource->startedAt,
                'finishedAt' => $resource->finishedAt,
                'durationSeconds' => $resource->durationSeconds,
                'correctAnswers' => $resource->correctAnswers,
                'score' => $resource->score,
            ];
            
            // Include answers map if available
            if ($resource->answers !== null) {
                $data['answers'] = $resource->answers;
            }
        } else {
            // Handle Eloquent Model (snake_case properties)
            $data = [
                'id' => (string) $resource->id,
                'userId' => (string) $resource->user_id,
                'examId' => (string) $resource->exam_id,
                'startedAt' => $resource->started_at ? $resource->started_at->toIso8601String() : null,
                'finishedAt' => $resource->finished_at ? $resource->finished_at->toIso8601String() : null,
                'durationSeconds' => $resource->duration_seconds,
                'correctAnswers' => $resource->correct_answers,
                'score' => $resource->score,
            ];

            // Include questions if requested (only for Eloquent models)
            if ($this->includeQuestions) {
                $data['questions'] = $this->getQuestionsWithConditionalVisibility();
                $data['answers'] = $this->getAnswersMap();
            }
        }

        return $data;
    }

    /**
     * Get questions with conditional answer visibility based on:
     * - Attempt finished status
     * - Exam feedback mode
     * - Which questions are answered (for immediate mode)
     *
     * @return array
     */
    protected function getQuestionsWithConditionalVisibility()
    {
        // Ensure exam and questions are loaded
        if (!$this->resource->relationLoaded('exam')) {
            $this->resource->load('exam.questions');
        } elseif (!$this->resource->exam->relationLoaded('questions')) {
            $this->resource->exam->load('questions');
        }

        $exam = $this->resource->exam;
        $isFinished = $this->resource->finished_at !== null;
        $feedbackMode = $exam->feedback_mode;
        
        // Get answered question IDs for immediate mode
        $answeredQuestionIds = [];
        if (!$isFinished && $feedbackMode === FeedbackMode::IMMEDIATE) {
            if (!$this->resource->relationLoaded('answers')) {
                $this->resource->load('answers');
            }
            $answeredQuestionIds = $this->resource->answers->pluck('question_id')->toArray();
        }

        return $exam->questions->map(function ($question) use ($isFinished, $feedbackMode, $answeredQuestionIds) {
            $showAnswer = $this->shouldShowAnswer($question->id, $isFinished, $feedbackMode, $answeredQuestionIds);
            return (new QuestionResource($question))->showAnswer($showAnswer);
        });
    }

    /**
     * Determine if the answer should be shown for a specific question.
     *
     * Rules:
     * - Finished attempt: always show answers for all questions
     * - Ongoing + immediate mode: show only for answered questions
     * - Ongoing + final mode: never show
     *
     * @param int $questionId
     * @param bool $isFinished
     * @param FeedbackMode|null $feedbackMode
     * @param array $answeredQuestionIds
     * @return bool
     */
    protected function shouldShowAnswer($questionId, $isFinished, $feedbackMode, $answeredQuestionIds)
    {
        // Finished attempt: always show all answers
        if ($isFinished) {
            return true;
        }

        // Ongoing attempt with final mode: never show
        if ($feedbackMode === FeedbackMode::FINAL || $feedbackMode === null) {
            return false;
        }

        // Ongoing attempt with immediate mode: show only for answered questions
        return in_array($questionId, $answeredQuestionIds);
    }

    /**
     * Get a map of question IDs to user answers.
     *
     * @return array
     */
    protected function getAnswersMap()
    {
        if (!$this->resource->relationLoaded('answers')) {
            $this->resource->load('answers');
        }

        $answers = [];
        foreach ($this->resource->answers as $answer) {
            $answers[(string) $answer->question_id] = $answer->chosen_answer;
        }

        return $answers;
    }
}
