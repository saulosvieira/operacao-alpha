<?php

namespace App\Domain\Edduz\Enums;

enum WebhookEventType: string
{
    case SUBSCRIPTION_CONFIRMED = 'subscription_confirmed';
    case SUBSCRIPTION_CANCELLED = 'subscription_cancelled';
    case SUBSCRIPTION_EXPIRED = 'subscription_expired';
}
