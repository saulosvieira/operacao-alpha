<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class TableHelper
{
    /**
     * Gera a URL para ordenação das colunas da tabela
     *
     * @param string $column
     * @return string
     */
    public static function sortUrl($column)
    {
        $direction = 'asc';

        if (Request::get('sort') === $column && Request::get('direction') === 'asc') {
            $direction = 'desc';
        }

        return Request::fullUrlWithQuery([
            'sort' => $column,
            'direction' => $direction,
            'page' => null
        ]);
    }

    /**
     * Retorna a direção da seta de ordenação
     *
     * @param string $column
     * @return string
     */
    public static function sortDirection($column)
    {
        if (Request::get('sort') !== $column) {
            return '';
        }

        return Request::get('direction') === 'asc' ? 'up' : 'down';
    }

    /**
     * Aplica a ordenação na query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $sortable
     * @param string|null $defaultSort
     * @param string $defaultDirection
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applySorting($query, $sortable = [], $defaultSort = null, $defaultDirection = 'asc')
    {
        $sort = Request::get('sort');
        $direction = strtolower(Request::get('direction', $defaultDirection));

        // Se não houver ordenação definida, usa a padrão
        if (!$sort && $defaultSort) {
            $sort = $defaultSort;
            $direction = $defaultDirection;
        }

        // Verifica se a coluna é ordenável
        if ($sort && in_array($sort, $sortable)) {
            return $query->orderBy($sort, in_array($direction, ['asc', 'desc']) ? $direction : 'asc');
        }

        return $query;
    }
}
