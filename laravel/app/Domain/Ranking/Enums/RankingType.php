<?php

namespace App\Domain\Ranking\Enums;

enum RankingType: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
}
