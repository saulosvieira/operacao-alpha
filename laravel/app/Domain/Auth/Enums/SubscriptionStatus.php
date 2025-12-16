<?php

namespace App\Domain\Auth\Enums;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case TRIAL = 'trial';
    case EXPIRED = 'expired';
}
