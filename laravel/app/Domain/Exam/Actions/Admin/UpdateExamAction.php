<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Exam\DTOs\ExamData;
use App\Domain\Exam\Models\Exam;

final class UpdateExamAction
{
    /**
     * Execute the action to update an existing exam
     *
     * @param int $examId
     * @param int $careerId
     * @param string $title
     * @param string|null $description
     * @param int $timeLimitMinutes
     * @param string $feedbackMode
     * @param bool $active
     * @param bool $isFree
     * @return ExamData
     */
    public function execute(
        int $examId,
        int $careerId,
        string $title,
        ?string $description = null,
        int $timeLimitMinutes = 60,
        string $feedbackMode = 'final',
        bool $active = true,
        bool $isFree = false
    ): ExamData {
        $exam = Exam::findOrFail($examId);

        $exam->update([
            'career_id' => $careerId,
            'title' => $title,
            'description' => $description,
            'time_limit_minutes' => $timeLimitMinutes,
            'feedback_mode' => $feedbackMode,
            'active' => $active,
            'is_free' => $isFree,
        ]);

        $exam->load('career');
        $exam->loadCount('questions');

        $careerData = null;
        if ($exam->career) {
            $careerData = new CareerData(
                id: $exam->career->id,
                name: $exam->career->name,
                description: $exam->career->description,
                active: $exam->career->active,
                createdAt: $exam->career->created_at->toIso8601String(),
                updatedAt: $exam->career->updated_at->toIso8601String(),
                slug: $exam->career->slug ?? '',
                examsCount: 0,
            );
        }

        return new ExamData(
            id: (string) $exam->id,
            careerId: (string) $exam->career_id,
            title: $exam->title,
            description: $exam->description,
            timeLimitMinutes: $exam->time_limit_minutes,
            active: $exam->active,
            totalQuestions: $exam->questions_count ?? 0,
            career: $careerData,
            isFree: $exam->is_free,
            feedbackMode: $exam->feedback_mode?->value ?? 'final',
        );
    }
}
