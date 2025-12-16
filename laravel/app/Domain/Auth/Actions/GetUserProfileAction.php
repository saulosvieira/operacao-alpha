<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\UserData;
use App\Domain\Auth\Repositories\UserRepository;

class GetUserProfileAction
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function execute(string $userId): ?UserData
    {
        return $this->repository->findById($userId);
    }
}
