<?php

namespace App\Domain\Subscription\DTOs;

use App\Domain\Auth\Enums\SubscriptionStatus;
use App\Domain\Subscription\Enums\PlanType;

class SubscriptionData
{
    public $userId;
    public $status;
    public $planType;
    public $expiresAt;
    public $platformId;

    public function __construct(
        string $userId,
        SubscriptionStatus $status,
        ?PlanType $planType,
        ?string $expiresAt,
        ?string $platformId
    ) {
        $this->userId = $userId;
        $this->status = $status;
        $this->planType = $planType;
        $this->expiresAt = $expiresAt;
        $this->platformId = $platformId;
    }

    public static function fromArray(array $data): self
    {
        $status = $data['status'];
        if (is_string($status)) {
            $status = SubscriptionStatus::from($status);
        }

        $planType = $data['plan_type'] ?? null;
        if (is_string($planType)) {
            $planType = PlanType::from($planType);
        }

        return new self(
            $data['user_id'],
            $status,
            $planType,
            $data['expires_at'] ?? null,
            $data['platform_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'status' => $this->status->value,
            'plan_type' => $this->planType ? $this->planType->value : null,
            'expires_at' => $this->expiresAt,
            'platform_id' => $this->platformId,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVE;
    }

    public function isExpired(): bool
    {
        if (!$this->expiresAt) {
            return false;
        }

        return now()->isAfter($this->expiresAt);
    }
}
