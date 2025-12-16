<?php

namespace App\Domain\Auth\DTOs;

use App\Domain\Auth\Enums\SubscriptionStatus;
use App\Domain\Auth\Enums\UserRole;

readonly class UserData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public UserRole $role,
        public SubscriptionStatus $subscriptionStatus,
        public ?string $subscriptionExpiresAt,
        public ?string $subscriptionPlatformId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            role: UserRole::from($data['role']),
            subscriptionStatus: SubscriptionStatus::from($data['subscription_status']),
            subscriptionExpiresAt: $data['subscription_expires_at'] ?? null,
            subscriptionPlatformId: $data['subscription_platform_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role->value,
            'subscription_status' => $this->subscriptionStatus->value,
            'subscription_expires_at' => $this->subscriptionExpiresAt,
            'subscription_platform_id' => $this->subscriptionPlatformId,
        ];
    }
}
