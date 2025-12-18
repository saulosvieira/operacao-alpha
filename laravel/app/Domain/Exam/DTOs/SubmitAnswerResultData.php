<?php

namespace App\Domain\Exam\DTOs;

readonly class SubmitAnswerResultData
{
    public function __construct(
        public AnswerData $answer,
        public ?string $correctAnswer = null,
        public ?string $explanation = null,
        public bool $showFeedback = false,
    ) {}
    
    public function toArray(): array
    {
        $data = [
            'success' => true,
            'answer' => $this->answer->toArray(),
        ];
        
        if ($this->showFeedback) {
            $data['correctAnswer'] = $this->correctAnswer;
            $data['explanation'] = $this->explanation;
            $data['isCorrect'] = $this->answer->correct;
        }
        
        return $data;
    }
}
