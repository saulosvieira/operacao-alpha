<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Exam\Models\Exam;
use Exception;

final class DeleteExamAction
{
    /**
     * Execute the action to delete an exam
     *
     * @param int $examId
     * @return void
     * @throws Exception
     */
    public function execute(int $examId): void
    {
        $exam = Exam::withCount('questions')->findOrFail($examId);

        if ($exam->questions_count > 0) {
            throw new Exception('Cannot delete exam with associated questions');
        }

        $exam->delete();
    }
}
