<?php

namespace App\Domain\Subscription\Actions;

use App\Domain\Subscription\DTOs\SubscriptionData;
use App\Domain\Subscription\Repositories\SubscriptionRepository;

class GetSubscriptionStatusAction
{
    private $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $userId): SubscriptionData
    {
        return $this->repository->getUserSubscription($userId);
    }
}
