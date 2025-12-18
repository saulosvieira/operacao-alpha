<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Exam\Models\Question;
use Illuminate\Database\Eloquent\Collection;

final class ListQuestionsForExamAction
{
    /**
     * Execute the action to list all questions for an exam ordered by question number
     *
     * @param int $examId
     * @return Collection<int, Question>
     */
    public function execute(int $examId): Collection
    {
        return Question::where('exam_id', $examId)
            ->orderBy('question_number', 'asc')
            ->get();
    }
}
