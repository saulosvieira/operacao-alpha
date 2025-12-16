<?php

namespace App\Domain\Performance\Actions;

use App\Domain\Performance\Repositories\PerformanceRepository;
use App\Domain\Performance\DTOs\StatisticsData;

class GetStatisticsAction
{
    public function __construct(
        private PerformanceRepository $repository
    ) {}

    public function execute(string $userId): StatisticsData
    {
        return $this->repository->getStatistics($userId);
    }
}
