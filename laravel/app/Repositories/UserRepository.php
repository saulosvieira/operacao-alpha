<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new User);
    }

    protected function applyFilters($query, array $filters)
    {
        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }
        
        if (! empty($filters['email'])) {
            $query->where('email', 'like', '%'.$filters['email'].'%');
        }
        
        if (! empty($filters['phone'])) {
            $query->where('phone', 'like', '%'.$filters['phone'].'%');
        }
        
        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        
        // Global search across multiple fields
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%')
                  ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    
    public function findByRole(string $role)
    {
        return $this->model->where('role', $role)->get();
    }
    
    public function getAdmins()
    {
        return $this->findByRole('admin');
    }
    
    public function getConsultors()
    {
        return $this->findByRole('consultor');
    }
}
