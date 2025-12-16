<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions;

use App\Domain\Career\Repositories\CareerRepository;
use Illuminate\Support\Collection;

final readonly class ListCareersAction
{
    public function __construct(
        private CareerRepository $repository
    ) {
    }

    /**
     * Execute the action to list all active careers
     *
     * @return Collection
     */
    public function execute(): Collection
    {
        return $this->repository->getAllActive();
    }
}
