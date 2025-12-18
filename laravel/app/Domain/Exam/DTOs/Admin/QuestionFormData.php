<?php

declare(strict_types=1);

namespace App\Domain\Exam\DTOs\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * DTO for admin question form data (create/update operations)
 */
readonly class QuestionFormData
{
    public function __construct(
        public int $questionNumber,
        public string $statement,
        public ?UploadedFile $statementImage,
        public ?string $existingStatementImage,
        public bool $removeStatementImage,
        public string $optionA,
        public ?UploadedFile $optionAImage,
        public ?string $existingOptionAImage,
        public bool $removeOptionAImage,
        public string $optionB,
        public ?UploadedFile $optionBImage,
        public ?string $existingOptionBImage,
        public bool $removeOptionBImage,
        public string $optionC,
        public ?UploadedFile $optionCImage,
        public ?string $existingOptionCImage,
        public bool $removeOptionCImage,
        public string $optionD,
        public ?UploadedFile $optionDImage,
        public ?string $existingOptionDImage,
        public bool $removeOptionDImage,
        public string $optionE,
        public ?UploadedFile $optionEImage,
        public ?string $existingOptionEImage,
        public bool $removeOptionEImage,
        public string $correctAnswer,
        public ?string $explanation,
    ) {}

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            questionNumber: (int) $request->input('question_number'),
            statement: $request->input('statement'),
            statementImage: $request->file('statement_image'),
            existingStatementImage: $request->input('existing_statement_image'),
            removeStatementImage: (bool) $request->input('remove_statement_image', false),
            optionA: $request->input('option_a'),
            optionAImage: $request->file('option_a_image'),
            existingOptionAImage: $request->input('existing_option_a_image'),
            removeOptionAImage: (bool) $request->input('remove_option_a_image', false),
            optionB: $request->input('option_b'),
            optionBImage: $request->file('option_b_image'),
            existingOptionBImage: $request->input('existing_option_b_image'),
            removeOptionBImage: (bool) $request->input('remove_option_b_image', false),
            optionC: $request->input('option_c'),
            optionCImage: $request->file('option_c_image'),
            existingOptionCImage: $request->input('existing_option_c_image'),
            removeOptionCImage: (bool) $request->input('remove_option_c_image', false),
            optionD: $request->input('option_d'),
            optionDImage: $request->file('option_d_image'),
            existingOptionDImage: $request->input('existing_option_d_image'),
            removeOptionDImage: (bool) $request->input('remove_option_d_image', false),
            optionE: $request->input('option_e'),
            optionEImage: $request->file('option_e_image'),
            existingOptionEImage: $request->input('existing_option_e_image'),
            removeOptionEImage: (bool) $request->input('remove_option_e_image', false),
            correctAnswer: $request->input('correct_answer'),
            explanation: $request->input('explanation'),
        );
    }

    /**
     * Convert to array for database storage (without images)
     */
    public function toArray(): array
    {
        return [
            'question_number' => $this->questionNumber,
            'statement' => $this->statement,
            'option_a' => $this->optionA,
            'option_b' => $this->optionB,
            'option_c' => $this->optionC,
            'option_d' => $this->optionD,
            'option_e' => $this->optionE,
            'correct_answer' => $this->correctAnswer,
            'explanation' => $this->explanation,
        ];
    }
}
