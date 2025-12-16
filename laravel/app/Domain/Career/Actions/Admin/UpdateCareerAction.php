<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;
use Illuminate\Support\Str;

final class UpdateCareerAction
{
    /**
     * Execute the action to update an existing career
     *
     * @param int $careerId
     * @param string $name
     * @param string|null $description
     * @param bool $active
     * @return CareerData
     */
    public function execute(int $careerId, string $name, ?string $description, bool $active): CareerData
    {
        $career = Career::findOrFail($careerId);

        $career->update([
            'name' => $name,
            'description' => $description,
            'slug' => Str::slug($name),
            'active' => $active,
        ]);

        $career->loadCount('exams');

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
