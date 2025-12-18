<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\AttemptRepository;
use App\Domain\Exam\Repositories\QuestionRepository;
use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\AnswerRepository;
use App\Domain\Exam\DTOs\AnswerData;
use App\Domain\Exam\DTOs\SubmitAnswerResultData;
use App\Domain\Exam\Enums\AnswerOption;
use App\Domain\Exam\Enums\FeedbackMode;

class SubmitAnswerAction
{
    public function __construct(
        private AttemptRepository $attemptRepository,
        private QuestionRepository $questionRepository,
        private ExamRepository $examRepository,
        private AnswerRepository $answerRepository,
    ) {}
    
    public function execute(
        string $attemptId,
        string $questionId,
        string $answer,
        int $timeSeconds
    ): SubmitAnswerResultData {
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
        
        $answerData = $this->answerRepository->createOrUpdate([
            'user_id' => $attempt->userId,
            'attempt_id' => $attemptId,
            'question_id' => $questionId,
            'chosen_answer' => $answer,
            'correct' => $correct,
            'time_seconds' => $timeSeconds,
        ]);
        
        // Check exam feedback mode for immediate feedback
        $exam = $this->examRepository->findById($attempt->examId);
        $showFeedback = $exam && $exam->feedbackMode === FeedbackMode::IMMEDIATE->value;
        
        return new SubmitAnswerResultData(
            answer: $answerData,
            correctAnswer: $showFeedback ? $question->correctAnswer : null,
            explanation: $showFeedback ? $question->explanation : null,
            showFeedback: $showFeedback,
        );
    }
}
