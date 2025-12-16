<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Repositories\CareerRepository;

final readonly class GetCareerDetailsAction
{
    public function __construct(
        private CareerRepository $repository
    ) {
    }

    /**
     * Execute the action to get career details
     */
    public function execute(int $careerId): ?CareerData
    {
        return $this->repository->findActiveById($careerId);
    }
}
