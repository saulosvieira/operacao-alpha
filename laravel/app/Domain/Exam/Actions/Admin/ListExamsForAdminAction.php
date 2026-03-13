<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Exam\DTOs\ExamData;
use App\Domain\Exam\Models\Exam;
use App\Domain\Shared\DTOs\ListFilterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ListExamsForAdminAction
{
    /**
     * Execute the action to list exams for admin panel
     *
     * @param ListFilterData $filter Dados do filtro
     * @return LengthAwarePaginator<ExamData>
     */
    public function execute(ListFilterData $filter): LengthAwarePaginator
    {
        $query = Exam::with('career')
            ->withCount('questions');

        // Aplica filtro de busca
        if ($filter->hasSearch()) {
            $query = $this->applySearch($query, $filter->search);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($filter->perPage)
            ->through(function (Exam $exam) {
                $careerData = null;
                if ($exam->career) {
                    $careerData = new CareerData(
                        id: $exam->career->id,
                        name: $exam->career->name,
                        description: $exam->career->description,
                        active: $exam->career->active,
                        createdAt: $exam->career->created_at->toIso8601String(),
                        updatedAt: $exam->career->updated_at->toIso8601String(),
                        slug: $exam->career->slug ?? '',
                        examsCount: 0,
                    );
                }

                return new ExamData(
                    id: (string) $exam->id,
                    careerId: (string) $exam->career_id,
                    title: $exam->title,
                    description: $exam->description,
                    timeLimitMinutes: $exam->time_limit_minutes,
                    active: $exam->active,
                    totalQuestions: $exam->questions_count ?? 0,
                    career: $careerData,
                    isFree: $exam->is_free,
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
            // Campos da tabela exams
            $q->orWhereRaw('LOWER(title) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $searchLower . '%']);

            // Campos do relacionamento career
            $q->orWhereHas('career', function (Builder $cq) use ($searchLower) {
                $cq->whereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%']);
            });
        });
    }
}
