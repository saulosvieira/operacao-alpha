<?php

namespace App\Domain\Notification\Models;

use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcmToken extends Model
{
    protected $table = 'fcm_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'device_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
