<?php

namespace App\Domain\Approved\Actions;

use App\Domain\Approved\Repositories\ApprovedRepository;
use Illuminate\Support\Collection;

class ListApprovedAction
{
    public function __construct(
        private ApprovedRepository $repository
    ) {}

    /**
     * Execute the action to list approved candidates
     *
     * @param int|null $careerId Optional filter by career
     * @param int|null $year Optional filter by year
     * @return Collection
     */
    public function execute(?int $careerId = null, ?int $year = null): Collection
    {
        return $this->repository->findAll($careerId, $year);
    }
}
