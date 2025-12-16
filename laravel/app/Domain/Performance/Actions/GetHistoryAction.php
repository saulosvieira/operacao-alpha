<?php

namespace App\Domain\Performance\Actions;

use App\Domain\Performance\Repositories\PerformanceRepository;
use Illuminate\Support\Collection;

class GetHistoryAction
{
    public function __construct(
        private PerformanceRepository $repository
    ) {}

    public function execute(string $userId, int $limit = 20): Collection
    {
        return $this->repository->getHistory($userId, $limit);
    }
}
