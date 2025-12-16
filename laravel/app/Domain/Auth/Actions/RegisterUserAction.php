<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\RegisterData;
use App\Domain\Auth\DTOs\UserData;
use App\Domain\Auth\Repositories\UserRepository;

class RegisterUserAction
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function execute(RegisterData $registerData): array
    {
        $userData = $this->userRepository->create([
            'name' => $registerData->name,
            'email' => $registerData->email,
            'password' => $registerData->password,
            'phone' => $registerData->phone,
            'role' => 'user',
            'subscription_status' => 'trial',
        ]);

        $user = \App\Domain\Auth\Models\User::find($userData->id);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $userData,
            'token' => $token,
        ];
    }
}
