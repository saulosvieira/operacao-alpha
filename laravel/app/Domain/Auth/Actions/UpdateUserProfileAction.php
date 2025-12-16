<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\UserData;
use App\Domain\Auth\Repositories\UserRepository;

class UpdateUserProfileAction
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function execute(string $userId, array $data): ?UserData
    {
        // Validate that user exists
        $existingUser = $this->repository->findById($userId);
        
        if (!$existingUser) {
            throw new \Exception('User not found');
        }

        // Check if email is being changed and if it's already taken by another user
        if (isset($data['email']) && $data['email'] !== $existingUser->email) {
            $userWithEmail = $this->repository->findByEmail($data['email']);
            if ($userWithEmail && $userWithEmail->id !== $userId) {
                throw new \Exception('Email already in use');
            }
        }

        return $this->repository->update($userId, $data);
    }
}
