<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;

final class GetCareerForEditAction
{
    /**
     * Execute the action to get a career for editing
     *
     * @param int $careerId
     * @return CareerData
     */
    public function execute(int $careerId): CareerData
    {
        $career = Career::withCount('exams')->findOrFail($careerId);

        return new CareerData(
            id: $career->id,
            name: $career->name,
            description: $career->description,
            active: $career->active,
            createdAt: $career->created_at->toIso8601String(),
            updatedAt: $career->updated_at->toIso8601String(),
            slug: $career->slug ?? '',
            examsCount: $career->exams_count ?? 0,
        );
    }
}
