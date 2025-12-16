<?php

namespace App\Http\Resources\Ranking;

use Illuminate\Http\Resources\Json\JsonResource;

class RankingEntryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'userId' => $this->userId,
            'userName' => $this->userName,
            'userAvatar' => $this->userAvatar,
            'score' => $this->score,
            'totalExams' => $this->totalExams,
            'correctAnswers' => $this->correctAnswers,
            'position' => $this->position,
            'careerId' => $this->careerId,
        ];
    }
}
