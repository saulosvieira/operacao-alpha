<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\Models\Career;
use App\Domain\Shared\DTOs\ListFilterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ListCareersForAdminAction
{
    /**
     * Execute the action to list careers for admin panel
     *
     * @param ListFilterData $filter Dados do filtro
     * @return LengthAwarePaginator<CareerData>
     */
    public function execute(ListFilterData $filter): LengthAwarePaginator
    {
        $query = Career::withCount('exams');

        // Aplica filtro de busca
        if ($filter->hasSearch()) {
            $query = $this->applySearch($query, $filter->search);
        }

        return $query
            ->orderBy('name')
            ->paginate($filter->perPage)
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

    /**
     * Aplica filtro de busca nos campos exibidos
     */
    private function applySearch(Builder $query, string $search): Builder
    {
        $searchLower = mb_strtolower(trim($search));

        return $query->where(function (Builder $q) use ($searchLower) {
            $q->orWhereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(slug) LIKE ?', ['%' . $searchLower . '%']);
        });
    }
}
