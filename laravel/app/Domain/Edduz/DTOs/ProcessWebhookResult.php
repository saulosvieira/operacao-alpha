<?php

namespace App\Domain\Edduz\DTOs;

use App\Domain\Edduz\Enums\WebhookProcessingStatus;

class ProcessWebhookResult
{
    public function __construct(
        public readonly int $httpStatus,
        public readonly string $message,
        public readonly WebhookProcessingStatus $processingStatus,
    ) {}
}
