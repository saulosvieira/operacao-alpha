<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Exam\DTOs\ExamData;
use App\Domain\Exam\Models\Exam;

final class GetExamForEditAction
{
    /**
     * Execute the action to get a single exam for editing
     *
     * @param int $examId
     * @return ExamData
     */
    public function execute(int $examId): ExamData
    {
        $exam = Exam::with('career')->withCount('questions')->findOrFail($examId);

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
