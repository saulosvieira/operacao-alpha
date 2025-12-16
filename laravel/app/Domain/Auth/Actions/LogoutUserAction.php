<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Models\User;

class LogoutUserAction
{
    public function execute(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
