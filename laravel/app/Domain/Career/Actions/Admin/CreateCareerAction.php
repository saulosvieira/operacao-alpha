<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;
use Illuminate\Support\Str;

final class CreateCareerAction
{
    /**
     * Execute the action to create a new career
     *
     * @param string $name
     * @param string|null $description
     * @param bool $active
     * @return CareerData
     */
    public function execute(string $name, ?string $description, bool $active): CareerData
    {
        $career = Career::create([
            'name' => $name,
            'description' => $description,
            'slug' => Str::slug($name),
            'active' => $active,
        ]);

        return new CareerData(
            id: $career->id,
            name: $career->name,
            description: $career->description,
            active: $career->active,
            createdAt: $career->created_at->toIso8601String(),
            updatedAt: $career->updated_at->toIso8601String(),
            slug: $career->slug ?? '',
            examsCount: 0,
        );
    }
}
