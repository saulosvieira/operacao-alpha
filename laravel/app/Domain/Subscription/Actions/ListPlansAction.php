<?php

namespace App\Domain\Subscription\Actions;

use App\Domain\Subscription\Repositories\SubscriptionRepository;
use Illuminate\Support\Collection;

class ListPlansAction
{
    private $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): Collection
    {
        return $this->repository->getPlans();
    }
}
