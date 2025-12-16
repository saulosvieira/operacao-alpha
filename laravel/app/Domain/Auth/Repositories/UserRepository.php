<?php

namespace App\Domain\Auth\Repositories;

use App\Domain\Auth\DTOs\UserData;
use App\Domain\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findByEmail(string $email): ?UserData
    {
        $user = User::where('email', $email)->first();

        return $user ? $this->toDTO($user) : null;
    }

    public function findById(string $id): ?UserData
    {
        $user = User::find($id);

        return $user ? $this->toDTO($user) : null;
    }

    public function create(array $data): UserData
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'user',
            'subscription_status' => $data['subscription_status'] ?? 'inactive',
        ]);

        return $this->toDTO($user);
    }

    public function update(string $id, array $data): ?UserData
    {
        $user = User::find($id);

        if (! $user) {
            return null;
        }

        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }

        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }

        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (! empty($updateData)) {
            $user->update($updateData);
        }

        return $this->toDTO($user->fresh());
    }

    public function delete(string $id): bool
    {
        $user = User::find($id);

        if (! $user) {
            return false;
        }

        return $user->delete();
    }

    private function toDTO(User $user): UserData
    {
        return UserData::fromArray([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'subscription_status' => $user->subscription_status,
            'subscription_expires_at' => $user->subscription_expires_at?->toIso8601String(),
            'subscription_platform_id' => $user->subscription_platform_id,
        ]);
    }
}
