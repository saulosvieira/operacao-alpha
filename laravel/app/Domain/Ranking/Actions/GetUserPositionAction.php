<?php

namespace App\Domain\Ranking\Actions;

use App\Domain\Ranking\Repositories\RankingRepository;
use App\Domain\Ranking\DTOs\RankingEntryData;
use App\Domain\Ranking\Enums\RankingType;

class GetUserPositionAction
{
    public function __construct(
        private RankingRepository $rankingRepository,
    ) {}

    public function execute(
        string $userId,
        RankingType $type,
        ?string $careerId = null
    ): ?RankingEntryData {
        return $this->rankingRepository->getUserPosition($userId, $type, $careerId);
    }
}
