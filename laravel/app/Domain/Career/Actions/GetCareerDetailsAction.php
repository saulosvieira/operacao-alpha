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
     * Accepts either numeric ID or slug
     */
    public function execute(int|string $identifier): ?CareerData
    {
        if (is_int($identifier)) {
            return $this->repository->findActiveById($identifier);
        }
        
        return $this->repository->findByIdOrSlug($identifier);
    }
}
