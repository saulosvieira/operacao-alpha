<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;
use App\Domain\Notification\DTOs\SubscriptionData;

class SubscribeToNotificationsAction
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    public function execute(string $userId, array $subscriptionData): SubscriptionData
    {
        // Check if subscription already exists for this endpoint
        $existing = $this->repository->findByEndpoint($subscriptionData['endpoint']);

        if ($existing) {
            // If it exists but for a different user, delete it first
            if ($existing->userId !== $userId) {
                $this->repository->delete($subscriptionData['endpoint']);
            } else {
                // Already subscribed, return existing
                return $existing;
            }
        }

        // Create new subscription
        return $this->repository->create([
            'user_id' => $userId,
            'endpoint' => $subscriptionData['endpoint'],
            'public_key' => $subscriptionData['keys']['p256dh'],
            'auth_token' => $subscriptionData['keys']['auth'],
        ]);
    }
}
