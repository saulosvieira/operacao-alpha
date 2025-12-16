<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'careerId' => $this->careerId,
            'title' => $this->title,
            'description' => $this->description,
            'durationMin' => $this->timeLimitMinutes,
            'numQuestions' => $this->totalQuestions,
            'active' => $this->active,
            'isFree' => $this->is_free,
        ];
    }
}
