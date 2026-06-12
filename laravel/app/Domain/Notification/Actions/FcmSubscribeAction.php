<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Models\FcmToken;

class FcmSubscribeAction
{
    /**
     * Register or update an FCM token for a device.
     *
     * Uses updateOrCreate with device_id as key to avoid duplicates —
     * each device can only have one active token.
     */
    public function execute(string $userId, string $token, string $deviceId): FcmToken
    {
        return FcmToken::updateOrCreate(
            ['device_id' => $deviceId],
            [
                'user_id' => $userId,
                'token' => $token,
            ]
        );
    }
}
