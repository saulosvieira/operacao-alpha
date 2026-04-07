<?php

namespace App\Domain\Exam\DTOs;

readonly class QuestionData
{
    public function __construct(
        public string $id,
        public string $examId,
        public int $questionNumber,
        public string $statement,
        public ?string $statementImage,
        public array $options,
        public ?string $correctAnswer,
        public ?string $explanation,
        public ?string $supportText,
        public ?string $supportPdfUrl,
        public ?int $year,
        public ?string $examBoard,
    ) {}
    
    public static function fromArray(array $data): self
    {
        $options = [
            'A' => [
                'text' => $data['option_a'],
                'image' => $data['option_a_image'] ?? null,
            ],
            'B' => [
                'text' => $data['option_b'],
                'image' => $data['option_b_image'] ?? null,
            ],
            'C' => [
                'text' => $data['option_c'],
                'image' => $data['option_c_image'] ?? null,
            ],
            'D' => [
                'text' => $data['option_d'],
                'image' => $data['option_d_image'] ?? null,
            ],
            'E' => [
                'text' => $data['option_e'],
                'image' => $data['option_e_image'] ?? null,
            ],
        ];
        
        return new self(
            id: $data['id'],
            examId: $data['exam_id'],
            questionNumber: $data['question_number'],
            statement: $data['statement'],
            statementImage: $data['statement_image'] ?? null,
            options: $options,
            correctAnswer: $data['correct_answer'] ?? null,
            explanation: $data['explanation'] ?? null,
            supportText: $data['support_text'] ?? null,
            supportPdfUrl: $data['support_pdf_url'] ?? null,
            year: isset($data['year']) ? (int) $data['year'] : null,
            examBoard: $data['exam_board'] ?? null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'examId' => $this->examId,
            'questionNumber' => $this->questionNumber,
            'statement' => $this->statement,
            'statementImage' => $this->statementImage,
            'options' => $this->options,
            'correctAnswer' => $this->correctAnswer,
            'explanation' => $this->explanation,
            'supportText' => $this->supportText,
            'supportPdfUrl' => $this->supportPdfUrl,
            'year' => $this->year,
            'examBoard' => $this->examBoard,
        ];
    }
}
