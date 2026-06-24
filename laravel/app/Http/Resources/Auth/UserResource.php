<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'subscriptionStatus' => $this->subscription_status,
            'subscriptionExpiresAt' => $this->subscription_expires_at,
            'subscriptionPlatformId' => $this->subscription_platform_id !== null
                ? (string) $this->subscription_platform_id
                : null,
        ];
    }
}
