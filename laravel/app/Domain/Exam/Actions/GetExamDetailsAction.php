<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Repositories\ExamRepository;

class GetExamDetailsAction
{
    /**
     * @var ExamRepository
     */
    private $examRepository;

    public function __construct(ExamRepository $examRepository)
    {
        $this->examRepository = $examRepository;
    }
    
    /**
     * Get exam details with questions eager loaded.
     *
     * @param string $examId
     * @param bool $includeAnswers Whether to include correct answers and explanations
     * @return Exam|null
     */
    public function execute(string $examId, bool $includeAnswers = false): ?Exam
    {
        $exam = $this->examRepository->findByIdWithQuestions($examId);
        
        if (!$exam) {
            return null;
        }
        
        // The includeAnswers flag is now handled by the Resource layer
        // via QuestionResource->showAnswer() method
        
        return $exam;
    }
}
