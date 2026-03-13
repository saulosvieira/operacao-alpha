<?php

declare(strict_types=1);

namespace App\Domain\Shared\DTOs;

final class ListFilterData
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $sortBy = null,
        public readonly string $sortOrder = 'asc',
        public readonly int $perPage = 15,
        public readonly ?array $filters = null,
    ) {
    }

    public function hasSearch(): bool
    {
        return !empty($this->search);
    }

    public function hasSorting(): bool
    {
        return !empty($this->sortBy);
    }
}
