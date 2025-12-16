<?php

namespace App\Domain\Subscription\Enums;

enum PlanType: string
{
    case FREE = 'free';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
