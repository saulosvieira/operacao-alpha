<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\QuestionRepository;
use App\Domain\Exam\DTOs\ExamData;

class GetExamDetailsAction
{
    public function __construct(
        private ExamRepository $examRepository,
        private QuestionRepository $questionRepository
    ) {}
    
    public function execute(string $examId, bool $includeAnswers = false): ?array
    {
        $exam = $this->examRepository->findById($examId);
        
        if (!$exam) {
            return null;
        }
        
        $questions = $this->questionRepository->findByExam($examId);
        
        // Se não incluir respostas, remover correct_answer e explanation
        if (!$includeAnswers) {
            $questions = $questions->map(function ($question) {
                $data = $question->toArray();
                $data['correctAnswer'] = null;
                $data['explanation'] = null;
                return $data;
            });
        }
        
        return [
            'exam' => $exam->toArray(),
            'questions' => $questions->toArray(),
        ];
    }
}
