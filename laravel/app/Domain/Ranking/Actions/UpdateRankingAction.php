<?php

namespace App\Domain\Ranking\Actions;

use App\Domain\Ranking\Models\Ranking;
use App\Domain\Ranking\Repositories\ResultRepository;
use App\Domain\Ranking\Enums\RankingType;
use Illuminate\Support\Facades\DB;

class UpdateRankingAction
{
    public function __construct(
        private ResultRepository $resultRepository,
    ) {}

    /**
     * Update rankings for all users based on recent results
     * This can be called by a scheduled job
     */
    public function execute(RankingType $type, ?string $careerId = null): int
    {
        $startDate = $this->getStartDateForType($type);
        $results = $this->resultRepository->getRecentResults($startDate, $careerId);

        // Group results by user
        $userStats = $results->groupBy('user_id')->map(function ($userResults) {
            return [
                'average_score' => $userResults->avg('score'),
                'total_exams' => $userResults->count(),
                'total_correct' => $userResults->sum('correct_answers'),
            ];
        });

        $updatedCount = 0;

        DB::transaction(function () use ($userStats, $type, $careerId, &$updatedCount) {
            foreach ($userStats as $userId => $stats) {
                Ranking::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'type' => $type->value,
                        'career_id' => $careerId,
                    ],
                    [
                        'total_score' => $stats['average_score'],
                        'average_correct' => $stats['total_correct'] / max($stats['total_exams'], 1),
                        'total_exams' => $stats['total_exams'],
                        'calculated_at' => now(),
                    ]
                );
                $updatedCount++;
            }

            // Update positions based on scores
            $this->updatePositions($type, $careerId);
        });

        return $updatedCount;
    }

    private function updatePositions(RankingType $type, ?string $careerId): void
    {
        $query = Ranking::query()
            ->where('type', $type->value);

        if ($careerId) {
            $query->where('career_id', $careerId);
        }

        $rankings = $query
            ->orderByDesc('total_score')
            ->orderByDesc('total_exams')
            ->get();

        foreach ($rankings as $index => $ranking) {
            $ranking->update(['position' => $index + 1]);
        }
    }

    private function getStartDateForType(RankingType $type): \DateTime
    {
        return match ($type) {
            RankingType::DAILY => now()->startOfDay(),
            RankingType::WEEKLY => now()->startOfWeek(),
            RankingType::MONTHLY => now()->startOfMonth(),
        };
    }
}
