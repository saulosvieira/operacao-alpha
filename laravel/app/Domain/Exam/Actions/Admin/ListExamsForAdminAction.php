<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Exam\DTOs\ExamData;
use App\Domain\Exam\Models\Exam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListExamsForAdminAction
{
    /**
     * Execute the action to list exams for admin panel
     *
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator<ExamData>
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Exam::with('career')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->through(function (Exam $exam) {
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
                );
            });
    }
}
