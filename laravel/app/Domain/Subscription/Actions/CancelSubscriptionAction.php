<?php

namespace App\Domain\Subscription\Actions;

use App\Domain\Subscription\DTOs\SubscriptionData;
use App\Domain\Subscription\Repositories\SubscriptionRepository;

class CancelSubscriptionAction
{
    private $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $userId): SubscriptionData
    {
        $subscription = $this->repository->getUserSubscription($userId);

        if (!$subscription->isActive()) {
            throw new \Exception('No active subscription to cancel');
        }

        return $this->repository->cancelSubscription($userId);
    }
}
