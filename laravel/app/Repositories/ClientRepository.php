<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository extends BaseRepository
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }
    
    public function paginateWithFilters(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->newQuery();
        $query = $this->applyFilters($query, $filters);
        
        // Apply sorting after filters but before pagination
        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';
        
        // Verificar se a coluna de ordenação é válida
        $validSorts = ['name', 'email', 'document', 'phone', 'created_at', 'updated_at'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($perPage)->withQueryString();
    }
    
    public function findByEmail(string $email): ?Client
    {
        return $this->model->where('email', $email)->first();
    }
    
    public function findByDocument(string $document): ?Client
    {
        return $this->model->where('document', $document)->first();
    }
    
    public function getRecentClients(int $limit = 10)
    {
        return $this->model->latest()->limit($limit)->get();
    }

    protected function applyFilters($query, array $filters)
    {
        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        if (! empty($filters['document'])) {
            $query->where('document', 'like', '%'.$filters['document'].'%');
        }

        if (! empty($filters['email'])) {
            $query->where('email', 'like', '%'.$filters['email'].'%');
        }
        
        if (! empty($filters['phone'])) {
            $query->where('phone', 'like', '%'.$filters['phone'].'%');
        }
        
        if (! empty($filters['address'])) {
            $query->where('address', 'like', '%'.$filters['address'].'%');
        }
        
        // Global search across multiple fields
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%')
                  ->orWhere('document', 'like', '%'.$search.'%')
                  ->orWhere('phone', 'like', '%'.$search.'%')
                  ->orWhere('address', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }
}
