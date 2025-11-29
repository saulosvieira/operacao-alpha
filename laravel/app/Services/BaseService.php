<?php

namespace App\Services;

use App\Repositories\BaseRepository;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listPaginatedWithFilters(array $filters): array
    {
        $result = $this->repository->paginateWithFilters($filters);
        $sort = $filters['sort'] ?? 'id';
        $direction = $filters['direction'] ?? 'asc';

        return [$result, $sort, $direction];
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
