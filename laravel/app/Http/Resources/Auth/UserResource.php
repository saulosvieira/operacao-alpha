<?php

namespace App\Http\Resources\Auth;

use App\Domain\Auth\DTOs\UserData;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        // Support both UserData DTO (login/register) and User Model (me).
        if ($this->resource instanceof UserData) {
            return [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'email' => $this->resource->email,
                'phone' => $this->resource->phone,
                'role' => $this->resource->role->value,
                'subscriptionStatus' => $this->resource->subscriptionStatus->value,
                'subscriptionExpiresAt' => $this->resource->subscriptionExpiresAt,
                'subscriptionPlatformId' => $this->resource->subscriptionPlatformId,
            ];
        }

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
