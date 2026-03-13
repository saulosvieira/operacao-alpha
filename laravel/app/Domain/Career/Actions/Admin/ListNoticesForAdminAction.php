<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\DTOs\NoticeData;
use App\Domain\Career\Models\Notice;
use App\Domain\Shared\DTOs\ListFilterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ListNoticesForAdminAction
{
    /**
     * Execute the action to list notices for admin panel
     *
     * @param ListFilterData $filter Dados do filtro
     * @return LengthAwarePaginator<NoticeData>
     */
    public function execute(ListFilterData $filter): LengthAwarePaginator
    {
        $query = Notice::with('career');

        // Aplica filtro de busca
        if ($filter->hasSearch()) {
            $query = $this->applySearch($query, $filter->search);
        }

        return $query
            ->orderBy('publication_date', 'desc')
            ->paginate($filter->perPage)
            ->through(function (Notice $notice) {
                $careerData = null;
                if ($notice->career) {
                    $careerData = new CareerData(
                        id: $notice->career->id,
                        name: $notice->career->name,
                        description: $notice->career->description,
                        active: $notice->career->active,
                        createdAt: $notice->career->created_at->toIso8601String(),
                        updatedAt: $notice->career->updated_at->toIso8601String(),
                        slug: $notice->career->slug ?? '',
                        examsCount: 0,
                    );
                }

                return new NoticeData(
                    id: $notice->id,
                    careerId: $notice->career_id,
                    title: $notice->title,
                    description: $notice->description,
                    examDate: $notice->publication_date?->toIso8601String(),
                    registrationStart: $notice->registration_start?->toIso8601String(),
                    registrationEnd: $notice->registration_end?->toIso8601String(),
                    pdfUrl: $notice->pdf_url,
                    active: $notice->active,
                    createdAt: $notice->created_at->toIso8601String(),
                    updatedAt: $notice->updated_at->toIso8601String(),
                    career: $careerData,
                );
            });
    }

    /**
     * Aplica filtro de busca nos campos exibidos
     */
    private function applySearch(Builder $query, string $search): Builder
    {
        $searchLower = mb_strtolower(trim($search));

        return $query->where(function (Builder $q) use ($searchLower) {
            // Campos da tabela notices
            $q->orWhereRaw('LOWER(title) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $searchLower . '%']);

            // Campos do relacionamento career
            $q->orWhereHas('career', function (Builder $cq) use ($searchLower) {
                $cq->whereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%']);
            });
        });
    }
}
