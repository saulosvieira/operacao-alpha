<?php

namespace App\Domain\Ranking\DTOs;

use App\Domain\Ranking\Enums\RankingType;
use Illuminate\Support\Collection;

readonly class RankingData
{
    public function __construct(
        public RankingType $type,
        public Collection $entries,
        public ?string $careerId = null,
        public ?string $careerName = null,
        public int $totalEntries = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        $entries = collect($data['entries'] ?? [])
            ->map(fn($entry) => RankingEntryData::fromArray($entry));

        return new self(
            type: RankingType::from($data['type']),
            entries: $entries,
            careerId: $data['career_id'] ?? null,
            careerName: $data['career_name'] ?? null,
            totalEntries: (int) ($data['total_entries'] ?? $entries->count()),
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'entries' => $this->entries->map(fn($entry) => $entry->toArray())->toArray(),
            'career_id' => $this->careerId,
            'career_name' => $this->careerName,
            'total_entries' => $this->totalEntries,
        ];
    }
}
