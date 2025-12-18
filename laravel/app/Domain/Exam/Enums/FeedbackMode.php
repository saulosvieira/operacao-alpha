<?php

namespace App\Domain\Exam\Enums;

enum FeedbackMode: string
{
    case IMMEDIATE = 'immediate';
    case FINAL = 'final';
}
