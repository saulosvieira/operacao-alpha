<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;
use Illuminate\Support\Collection;

final class ListActiveCareersAction
{
    /**
     * Execute the action to list active careers for dropdowns
     *
     * @return Collection<int, CareerData>
     */
    public function execute(): Collection
    {
        return Career::where('active', true)
            ->orderBy('name')
            ->get()
            ->map(fn(Career $career) => new CareerData(
                id: $career->id,
                name: $career->name,
                description: $career->description,
                active: $career->active,
                createdAt: $career->created_at->toIso8601String(),
                updatedAt: $career->updated_at->toIso8601String(),
                slug: $career->slug ?? '',
                examsCount: 0,
            ));
    }
}
