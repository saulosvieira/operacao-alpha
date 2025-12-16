<?php

namespace App\Domain\Notification\DTOs;

readonly class SubscriptionData
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $endpoint,
        public string $publicKey,
        public string $authToken,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['user_id'],
            endpoint: $data['endpoint'],
            publicKey: $data['public_key'],
            authToken: $data['auth_token'],
            createdAt: $data['created_at'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'endpoint' => $this->endpoint,
            'public_key' => $this->publicKey,
            'auth_token' => $this->authToken,
            'created_at' => $this->createdAt,
        ];
    }
}
