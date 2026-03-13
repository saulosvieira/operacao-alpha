<?php

declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait para aplicar filtros de busca em queries de listagem
 * 
 * Uso:
 * 1. Adicione o trait na sua model ou use em uma query
 * 2. Chame applySearchFilter passando os campos que devem ser pesquisados
 */
trait FilterableBySearch
{
    /**
     * Aplica filtro de busca na query
     *
     * @param Builder $query A query builder instance
     * @param string|null $search Termo de busca
     * @param array<string> $fields Campos para pesquisar (ex: ['name', 'email'])
     * @param array<string, string> $relationFields Campos de relacionamentos (ex: ['career' => ['name', 'description']])
     * @return Builder
     */
    public function scopeApplySearchFilter(
        Builder $query,
        ?string $search,
        array $fields = [],
        array $relationFields = []
    ): Builder {
        if (empty($search)) {
            return $query;
        }

        $search = trim($search);
        $searchLower = mb_strtolower($search);

        return $query->where(function (Builder $q) use ($search, $searchLower, $fields, $relationFields) {
            // Busca nos campos da própria tabela
            foreach ($fields as $field) {
                $q->orWhereRaw(
                    'LOWER(' . $this->qualifyColumn($field) . ') LIKE ?',
                    ['%' . $searchLower . '%']
                );
            }

            // Busca nos relacionamentos
            foreach ($relationFields as $relation => $relationFieldList) {
                $q->orWhereHas($relation, function (Builder $rq) use ($searchLower, $relationFieldList) {
                    foreach ($relationFieldList as $field) {
                        $rq->orWhereRaw(
                            'LOWER(' . $field . ') LIKE ?',
                            ['%' . $searchLower . '%']
                        );
                    }
                });
            }
        });
    }

    /**
     * Aplica ordenação dinâmica
     *
     * @param Builder $query
     * @param string|null $sortBy Campo para ordenar
     * @param string $sortOrder Direção (asc ou desc)
     * @param array<string> $allowedFields Campos permitidos para ordenação
     * @return Builder
     */
    public function scopeApplySorting(
        Builder $query,
        ?string $sortBy,
        string $sortOrder = 'asc',
        array $allowedFields = []
    ): Builder {
        if (empty($sortBy) || !in_array($sortBy, $allowedFields, true)) {
            return $query;
        }

        $direction = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortBy, $direction);
    }
}
