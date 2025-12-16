<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListCareersForAdminAction
{
    /**
     * Execute the action to list careers for admin panel
     *
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator<CareerData>
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Career::withCount('exams')
            ->orderBy('name')
            ->paginate($perPage)
            ->through(fn (Career $career) => new CareerData(
                id: $career->id,
                name: $career->name,
                description: $career->description,
                active: $career->active,
                createdAt: $career->created_at->toIso8601String(),
                updatedAt: $career->updated_at->toIso8601String(),
                slug: $career->slug ?? '',
                examsCount: $career->exams_count ?? 0,
            ));
    }
}
