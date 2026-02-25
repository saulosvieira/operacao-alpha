<?php

namespace App\Domain\Edduz\Models;

use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EdduzWebhookLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'event_type',
        'user_id',
        'payload',
        'ip_address',
        'headers',
        'processing_status',
        'error_message',
        'received_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'headers' => 'array',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
