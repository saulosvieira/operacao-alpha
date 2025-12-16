<?php

namespace App\Domain\Notification\Models;

use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSubscription extends Model
{
    protected $table = 'notification_subscriptions';

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
