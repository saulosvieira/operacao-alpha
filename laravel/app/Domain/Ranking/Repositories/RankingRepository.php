<?php

namespace App\Domain\Ranking\Repositories;

use App\Domain\Ranking\DTOs\RankingEntryData;
use App\Domain\Ranking\Enums\RankingType;
use App\Domain\Exam\Models\ExamResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RankingRepository
{
    public function getRanking(
        RankingType $type,
        ?string $careerId = null,
        int $limit = 100
    ): Collection {
        $dateFilter = $this->getDateFilterForType($type);

        // Use MAX score for ranking (requirement 8.4: best score for ranking)
        // Order by score descending, then by time ascending for tie-breaking (requirement 8.3)
        $query = ExamResult::query()
            ->select([
                'exam_results.user_id',
                DB::raw('users.name as user_name'),
                DB::raw('MAX(exam_results.score) as score'),
                DB::raw('MIN(exam_results.total_time_seconds) as best_time'),
                DB::raw('COUNT(DISTINCT exam_results.exam_id) as total_exams'),
                DB::raw('SUM(exam_results.correct_answers) as correct_answers'),
            ])
            ->join('users', 'exam_results.user_id', '=', 'users.id')
            ->where('exam_results.finished_at', '>=', $dateFilter);

        if ($careerId) {
            $query->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->where('exams.career_id', $careerId);
        }

        $results = $query
            ->groupBy('exam_results.user_id', 'users.name')
            ->orderByDesc('score')
            ->orderBy('best_time') // Ascending for tie-breaking (faster is better)
            ->limit($limit)
            ->get();

        return $results->map(function ($result, $index) use ($careerId) {
            return RankingEntryData::fromArray([
                'user_id' => $result->user_id,
                'user_name' => $result->user_name,
                'user_avatar' => null,
                'score' => $result->score,
                'total_exams' => $result->total_exams,
                'correct_answers' => $result->correct_answers,
                'position' => $index + 1,
                'career_id' => $careerId,
            ]);
        });
    }

    public function getUserPosition(
        string $userId,
        RankingType $type,
        ?string $careerId = null
    ): ?RankingEntryData {
        $dateFilter = $this->getDateFilterForType($type);

        // Use MAX score for ranking (requirement 8.4: best score for ranking)
        // Order by score descending, then by time ascending for tie-breaking (requirement 8.3)
        $query = ExamResult::query()
            ->select([
                'exam_results.user_id',
                DB::raw('users.name as user_name'),
                DB::raw('users.avatar_url as user_avatar'),
                DB::raw('MAX(exam_results.score) as score'),
                DB::raw('MIN(exam_results.total_time_seconds) as best_time'),
                DB::raw('COUNT(DISTINCT exam_results.exam_id) as total_exams'),
                DB::raw('SUM(exam_results.correct_answers) as correct_answers'),
            ])
            ->join('users', 'exam_results.user_id', '=', 'users.id')
            ->where('exam_results.finished_at', '>=', $dateFilter);

        if ($careerId) {
            $query->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->where('exams.career_id', $careerId);
        }

        $allResults = $query
            ->groupBy('exam_results.user_id', 'users.name', 'users.avatar_url')
            ->orderByDesc('score')
            ->orderBy('best_time') // Ascending for tie-breaking (faster is better)
            ->get();

        $userIndex = $allResults->search(function ($result) use ($userId) {
            return $result->user_id == $userId;
        });

        if ($userIndex === false) {
            return null;
        }

        $userResult = $allResults[$userIndex];

        return RankingEntryData::fromArray([
            'user_id' => $userResult->user_id,
            'user_name' => $userResult->user_name,
            'user_avatar' => $userResult->user_avatar,
            'score' => $userResult->score,
            'total_exams' => $userResult->total_exams,
            'correct_answers' => $userResult->correct_answers,
            'position' => $userIndex + 1,
            'career_id' => $careerId,
        ]);
    }

    private function getDateFilterForType(RankingType $type): \DateTime
    {
        return match ($type) {
            RankingType::DAILY => now()->startOfDay(),
            RankingType::WEEKLY => now()->startOfWeek(),
            RankingType::MONTHLY => now()->startOfMonth(),
        };
    }
}
