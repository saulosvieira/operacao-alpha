<?php

namespace App\Domain\Ranking\Actions;

use App\Domain\Ranking\Repositories\RankingRepository;
use App\Domain\Ranking\DTOs\RankingData;
use App\Domain\Ranking\Enums\RankingType;
use App\Domain\Career\Repositories\CareerRepository;

class GetRankingAction
{
    public function __construct(
        private RankingRepository $rankingRepository,
        private CareerRepository $careerRepository,
    ) {}

    public function execute(
        RankingType $type,
        ?string $careerId = null,
        int $limit = 100
    ): RankingData {
        $entries = $this->rankingRepository->getRanking($type, $careerId, $limit);

        $careerName = null;
        if ($careerId) {
            $career = $this->careerRepository->findById($careerId);
            $careerName = $career?->name;
        }

        return RankingData::fromArray([
            'type' => $type->value,
            'entries' => $entries->map(fn($entry) => $entry->toArray())->toArray(),
            'career_id' => $careerId,
            'career_name' => $careerName,
            'total_entries' => $entries->count(),
        ]);
    }
}
