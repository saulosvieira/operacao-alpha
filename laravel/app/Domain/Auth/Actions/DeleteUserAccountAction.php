<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Repositories\UserRepository;

class DeleteUserAccountAction
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function execute(string $userId): bool
    {
        $user = $this->repository->findById($userId);
        
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $this->repository->delete($userId);
    }
}
