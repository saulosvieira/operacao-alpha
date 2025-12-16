<?php

namespace App\Domain\Approved\DTOs;

readonly class ApprovedData
{
    public function __construct(
        public int $id,
        public int $careerId,
        public ?int $noticeId,
        public string $name,
        public ?int $position,
        public int $year,
        public ?string $careerName = null,
        public ?string $noticeName = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            careerId: $data['career_id'],
            noticeId: $data['notice_id'] ?? null,
            name: $data['name'],
            position: $data['position'] ?? null,
            year: $data['year'],
            careerName: $data['career_name'] ?? null,
            noticeName: $data['notice_name'] ?? null,
        );
    }

    public function toArray(): array
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
