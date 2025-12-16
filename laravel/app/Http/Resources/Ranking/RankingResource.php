<?php

namespace App\Http\Resources\Ranking;

use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => $this->type->value,
            'entries' => RankingEntryResource::collection($this->entries),
            'careerId' => $this->careerId,
            'careerName' => $this->careerName,
            'totalEntries' => $this->totalEntries,
        ];
    }
}
