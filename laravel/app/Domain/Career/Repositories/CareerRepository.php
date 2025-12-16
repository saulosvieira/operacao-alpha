<?php

declare(strict_types=1);

namespace App\Domain\Career\Repositories;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;
use Illuminate\Support\Collection;

final class CareerRepository
{
    /**
     * Get all active careers
     *
     * @return Collection<int, CareerData>
     */
    public function getAllActive(): Collection
    {
        return Career::where('active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Career $career) => $this->toDTO($career));
    }

    /**
     * Get all careers (including inactive)
     *
     * @return Collection<int, CareerData>
     */
    public function getAll(): Collection
    {
        return Career::orderBy('name')
            ->get()
            ->map(fn (Career $career) => $this->toDTO($career));
    }

    /**
     * Find career by ID
     */
    public function findById(int $id): ?CareerData
    {
        $career = Career::find($id);

        return $career ? $this->toDTO($career) : null;
    }

    /**
     * Find active career by ID
     */
    public function findActiveById(int $id): ?CareerData
    {
        $career = Career::where('id', $id)
            ->where('active', true)
            ->first();

        return $career ? $this->toDTO($career) : null;
    }

    /**
     * Convert Career model to CareerData DTO
     */
    private function toDTO(Career $career): CareerData
    {
        return new CareerData(
            id: $career->id,
            name: $career->name,
            description: $career->description,
            active: $career->active,
            createdAt: $career->created_at->toIso8601String(),
            updatedAt: $career->updated_at->toIso8601String(),
            slug: $career->slug ?? '',
            examsCount: $career->exams_count ?? $career->exams()->count(),
        );
    }
}
