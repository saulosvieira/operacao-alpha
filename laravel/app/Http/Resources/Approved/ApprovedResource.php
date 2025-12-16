<?php

declare(strict_types=1);

namespace App\Http\Resources\Approved;

use App\Domain\Approved\DTOs\ApprovedData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ApprovedData
 */
final class ApprovedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'career_id' => $this->careerId,
            'notice_id' => $this->noticeId,
            'name' => $this->name,
            'position' => $this->position,
            'year' => $this->year,
            'career_name' => $this->careerName,
            'notice_name' => $this->noticeName,
        ];
    }
}
