<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\AttemptRepository;
use App\Domain\Exam\Repositories\QuestionRepository;
use App\Domain\Exam\Repositories\AnswerRepository;
use App\Domain\Exam\DTOs\AnswerData;
use App\Domain\Exam\Enums\AnswerOption;

class SubmitAnswerAction
{
    public function __construct(
        private AttemptRepository $attemptRepository,
        private QuestionRepository $questionRepository,
        private AnswerRepository $answerRepository,
    ) {}
    
    public function execute(
        string $attemptId,
        string $questionId,
        string $answer,
        int $timeSeconds
    ): AnswerData {
        $attempt = $this->attemptRepository->findById($attemptId);
        
        if (!$attempt) {
            throw new \Exception('Attempt not found');
        }
        
        if ($attempt->finishedAt) {
            throw new \Exception('Attempt already finished');
        }
        
        $question = $this->questionRepository->findById($questionId);
        
        if (!$question) {
            throw new \Exception('Question not found');
        }
        
        // Validar se a resposta é válida (A, B, C, D, E)
        try {
            $answerEnum = AnswerOption::from($answer);
        } catch (\ValueError $e) {
            throw new \Exception('Invalid answer option');
        }
        
        $correct = $question->correctAnswer === $answer;
        
        return $this->answerRepository->createOrUpdate([
            'attempt_id' => $attemptId,
            'question_id' => $questionId,
            'chosen_answer' => $answer,
            'correct' => $correct,
            'time_seconds' => $timeSeconds,
        ]);
    }
}
