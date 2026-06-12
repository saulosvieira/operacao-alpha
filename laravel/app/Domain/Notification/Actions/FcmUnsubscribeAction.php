<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Models\FcmToken;

class FcmUnsubscribeAction
{
    /**
     * Remove an FCM token by device ID.
     */
    public function executeByDeviceId(string $deviceId): bool
    {
        return FcmToken::where('device_id', $deviceId)->delete() > 0;
    }

    /**
     * Remove an FCM token by token value.
     */
    public function executeByToken(string $token): bool
    {
        return FcmToken::where('token', $token)->delete() > 0;
    }

    /**
     * Remove all FCM tokens for a user.
     */
    public function executeByUser(string $userId): bool
    {
        return FcmToken::where('user_id', $userId)->delete() > 0;
    }
}
