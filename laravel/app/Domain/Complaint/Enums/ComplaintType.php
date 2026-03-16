<?php

namespace App\Domain\Complaint\Enums;

enum ComplaintType: string
{
    case INCORRECT_ANSWER = 'incorrect_answer';
    case AMBIGUOUS_STATEMENT = 'ambiguous_statement';
    case OUTDATED_QUESTION = 'outdated_question';
    case FORMATTING_ERROR = 'formatting_error';
    case OTHER = 'other';
}
