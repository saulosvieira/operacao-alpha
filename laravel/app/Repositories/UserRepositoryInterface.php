<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
    
    public function findByEmail(string $email): ?User;
    
    public function findByRole(string $role);
    
    public function getAdmins();
    
    public function getConsultors();
}
