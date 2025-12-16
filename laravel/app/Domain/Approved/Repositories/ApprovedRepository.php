<?php

namespace App\Domain\Approved\Repositories;

use App\Domain\Approved\Models\Approved;
use App\Domain\Approved\DTOs\ApprovedData;
use Illuminate\Support\Collection;

class ApprovedRepository
{
    /**
     * Find all approved candidates with optional filters
     *
     * @param int|null $careerId Filter by career
     * @param int|null $year Filter by year
     * @return Collection<ApprovedData>
     */
    public function findAll(?int $careerId = null, ?int $year = null): Collection
    {
        $query = Approved::with(['career', 'notice'])
            ->orderBy('year', 'desc')
            ->orderBy('position', 'asc');

        if ($careerId) {
            $query->where('career_id', $careerId);
        }

        if ($year) {
            $query->where('year', $year);
        }

        return $query->get()->map(fn($approved) => $this->toDTO($approved));
    }

    /**
     * Find approved candidate by ID
     *
     * @param int $id
     * @return ApprovedData|null
     */
    public function findById(int $id): ?ApprovedData
    {
        $approved = Approved::with(['career', 'notice'])->find($id);

        return $approved ? $this->toDTO($approved) : null;
    }

    /**
     * Get distinct years from approved records
     *
     * @return Collection<int>
     */
    public function getAvailableYears(): Collection
    {
        return Approved::distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    }

    /**
     * Convert Approved model to DTO
     *
     * @param Approved $approved
     * @return ApprovedData
     */
    private function toDTO(Approved $approved): ApprovedData
    {
        return ApprovedData::fromArray([
            'id' => $approved->id,
            'career_id' => $approved->career_id,
            'notice_id' => $approved->notice_id,
            'name' => $approved->name,
            'position' => $approved->position,
            'year' => $approved->year,
            'career_name' => $approved->career?->name,
            'notice_name' => $approved->notice?->title,
        ]);
    }
}
