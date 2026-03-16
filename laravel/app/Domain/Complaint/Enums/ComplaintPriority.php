<?php

namespace App\Domain\Complaint\Enums;

enum ComplaintPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
