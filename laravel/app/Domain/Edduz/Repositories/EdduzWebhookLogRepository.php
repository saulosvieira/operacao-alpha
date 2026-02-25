<?php

namespace App\Domain\Edduz\Repositories;

use App\Domain\Edduz\Enums\WebhookProcessingStatus;
use App\Domain\Edduz\Models\EdduzWebhookLog;
use Illuminate\Pagination\LengthAwarePaginator;

class EdduzWebhookLogRepository
{
    public function create(array $data): EdduzWebhookLog
    {
        return EdduzWebhookLog::create($data);
    }

    public function updateProcessingResult(int $id, WebhookProcessingStatus $status, ?string $errorMessage = null): void
    {
        EdduzWebhookLog::where('id', $id)->update([
            'processing_status' => $status->value,
            'error_message' => $errorMessage,
            'processed_at' => now(),
        ]);
    }

    public function findByTransactionId(string $transactionId): ?EdduzWebhookLog
    {
        return EdduzWebhookLog::where('transaction_id', $transactionId)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = EdduzWebhookLog::query()->orderBy('received_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('processing_status', $filters['status']);
        }

        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('received_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('received_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    public function findOrFail(int $id): EdduzWebhookLog
    {
        return EdduzWebhookLog::findOrFail($id);
    }
}
