<?php

namespace App\Domain\Notification\DTOs;

readonly class NotificationData
{
    public function __construct(
        public string $title,
        public string $body,
        public ?string $icon = null,
        public ?string $badge = null,
        public ?string $url = null,
        public ?array $data = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            body: $data['body'],
            icon: $data['icon'] ?? null,
            badge: $data['badge'] ?? null,
            url: $data['url'] ?? null,
            data: $data['data'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'badge' => $this->badge,
            'url' => $this->url,
            'data' => $this->data,
        ], fn($value) => $value !== null);
    }
}
