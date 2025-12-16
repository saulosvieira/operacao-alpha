<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions;

use App\Domain\Exam\Models\Exam;
use Illuminate\Support\Collection;

final class ListExamsByCareerAction
{
    /**
     * Execute the action to list all active exams for a career
     *
     * @return Collection
     */
    public function execute(int $careerId): Collection
    {
        return Exam::where('career_id', $careerId)
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Exam $exam) {
                return [
                    'id' => $exam->id,
                    'career_id' => $exam->career_id,
                    'title' => $exam->title,
                    'description' => $exam->description,
                    'time_limit_minutes' => $exam->time_limit_minutes,
                    'active' => $exam->active,
                    'questions_count' => $exam->questions()->count(),
                    'created_at' => $exam->created_at->toIso8601String(),
                    'updated_at' => $exam->updated_at->toIso8601String(),
                ];
            });
    }
}
