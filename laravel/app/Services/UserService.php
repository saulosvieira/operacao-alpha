<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function listAll()
    {
        return $this->repo->paginateWithFilters([], 15);
    }

    public function paginateWithFilters(array $filters, int $perPage = 15)
    {
        return $this->repo->paginateWithFilters($filters, $perPage);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function find($id)
    {
        return $this->repo->findById($id);
    }

    public function update($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $this->repo->delete($id);
    }
}
