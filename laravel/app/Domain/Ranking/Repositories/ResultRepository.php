<?php

namespace App\Domain\Ranking\Repositories;

use App\Domain\Exam\Models\ExamResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ResultRepository
{
    /**
     * Get aggregated results for a user within a time period
     */
    public function getUserResultsInPeriod(
        string $userId,
        \DateTime $startDate,
        ?string $careerId = null
    ): array {
        $query = ExamResult::query()
            ->where('user_id', $userId)
            ->where('finished_at', '>=', $startDate);

        if ($careerId) {
            $query->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->where('exams.career_id', $careerId);
        }

        $results = $query->get();

        return [
            'total_exams' => $results->count(),
            'average_score' => $results->avg('score') ?? 0,
            'total_correct_answers' => $results->sum('correct_answers'),
            'total_questions' => $results->sum('total_questions'),
        ];
    }

    /**
     * Get top performers in a time period
     */
    public function getTopPerformers(
        \DateTime $startDate,
        ?string $careerId = null,
        int $limit = 10
    ): Collection {
        $query = ExamResult::query()
            ->select([
                'user_id',
                DB::raw('AVG(score) as average_score'),
                DB::raw('COUNT(*) as total_exams'),
                DB::raw('SUM(correct_answers) as total_correct'),
            ])
            ->where('finished_at', '>=', $startDate);

        if ($careerId) {
            $query->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->where('exams.career_id', $careerId);
        }

        return $query
            ->groupBy('user_id')
            ->orderByDesc('average_score')
            ->orderByDesc('total_exams')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent results for ranking calculation
     */
    public function getRecentResults(
        \DateTime $startDate,
        ?string $careerId = null
    ): Collection {
        $query = ExamResult::query()
            ->with(['user', 'exam'])
            ->where('finished_at', '>=', $startDate);

        if ($careerId) {
            $query->whereHas('exam', function ($q) use ($careerId) {
                $q->where('career_id', $careerId);
            });
        }

        return $query->orderBy('finished_at', 'desc')->get();
    }
}
