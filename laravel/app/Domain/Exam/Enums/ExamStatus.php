<?php

namespace App\Domain\Exam\Enums;

enum ExamStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
