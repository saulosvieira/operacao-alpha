<?php

namespace App\Domain\Subscription\Actions;

use App\Domain\Subscription\DTOs\SubscriptionData;
use App\Domain\Subscription\Enums\PlanType;
use App\Domain\Subscription\Repositories\SubscriptionRepository;

class CreateSubscriptionAction
{
    private $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(
        string $userId,
        string $planId,
        string $platformId
    ): SubscriptionData {
        // Get the plan to validate it exists
        $plan = $this->repository->getPlanById($planId);

        if (!$plan) {
            throw new \Exception('Invalid plan selected');
        }

        // Don't allow subscribing to free plan
        if ($plan->type === PlanType::FREE) {
            throw new \Exception('Cannot subscribe to free plan');
        }

        return $this->repository->createSubscription(
            $userId,
            $plan->type,
            $platformId
        );
    }
}
