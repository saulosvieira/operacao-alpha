<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\LoginData;
use App\Domain\Auth\DTOs\UserData;
use App\Domain\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUserAction
{
    public function execute(LoginData $loginData): ?array
    {
        $user = User::where('email', $loginData->email)->first();

        if (! $user || ! Hash::check($loginData->password, $user->password)) {
            return null;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = UserData::fromArray([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'subscription_status' => $user->subscription_status,
            'subscription_expires_at' => $user->subscription_expires_at?->toIso8601String(),
            'subscription_platform_id' => $user->subscription_platform_id,
        ]);

        return [
            'user' => $userData,
            'token' => $token,
        ];
    }
}
