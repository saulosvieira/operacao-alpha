<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request): array
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
        ];
    }
}
