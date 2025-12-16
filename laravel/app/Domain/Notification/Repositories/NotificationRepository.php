<?php

namespace App\Domain\Notification\Repositories;

use App\Domain\Notification\Models\NotificationSubscription;
use App\Domain\Notification\DTOs\SubscriptionData;
use Illuminate\Support\Collection;

class NotificationRepository
{
    public function findByUser(string $userId): Collection
    {
        return NotificationSubscription::where('user_id', $userId)
            ->get()
            ->map(fn($subscription) => $this->toDTO($subscription));
    }

    public function findByEndpoint(string $endpoint): ?SubscriptionData
    {
        $subscription = NotificationSubscription::where('endpoint', $endpoint)->first();

        return $subscription ? $this->toDTO($subscription) : null;
    }

    public function create(array $data): SubscriptionData
    {
        $subscription = NotificationSubscription::create([
            'user_id' => $data['user_id'],
            'endpoint' => $data['endpoint'],
            'public_key' => $data['public_key'],
            'auth_token' => $data['auth_token'],
        ]);

        return $this->toDTO($subscription);
    }

    public function delete(string $endpoint): bool
    {
        return NotificationSubscription::where('endpoint', $endpoint)->delete() > 0;
    }

    public function deleteByUser(string $userId): bool
    {
        return NotificationSubscription::where('user_id', $userId)->delete() > 0;
    }

    public function findAll(): Collection
    {
        return NotificationSubscription::all()
            ->map(fn($subscription) => $this->toDTO($subscription));
    }

    private function toDTO(NotificationSubscription $subscription): SubscriptionData
    {
        return SubscriptionData::fromArray([
            'id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'endpoint' => $subscription->endpoint,
            'public_key' => $subscription->public_key,
            'auth_token' => $subscription->auth_token,
            'created_at' => $subscription->created_at->toIso8601String(),
        ]);
    }
}
