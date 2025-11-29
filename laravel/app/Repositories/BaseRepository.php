<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginateWithFilters(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->newQuery();
        $query = $this->applyFilters($query, $filters);
        $sort = $filters['sort'] ?? 'id';
        $direction = $filters['direction'] ?? 'asc';
        $query->orderBy($sort, $direction);

        return $query->paginate($perPage);
    }

    protected function applyFilters($query, array $filters)
    {
        // Para sobrescrever nos repositórios concretos
        return $query;
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->findById($id);
        $record->update($data);

        return $record;
    }

    public function delete($id)
    {
        $record = $this->findById($id);

        return $record->delete();
    }
}
