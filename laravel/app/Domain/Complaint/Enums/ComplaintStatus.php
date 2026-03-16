<?php

namespace App\Domain\Complaint\Enums;

enum ComplaintStatus: string
{
    case OPEN = 'open';
    case IN_REVIEW = 'in_review';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';
}
