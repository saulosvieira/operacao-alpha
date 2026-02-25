<?php

namespace App\Domain\Edduz\Actions;

use App\Domain\Auth\Enums\SubscriptionStatus;
use App\Domain\Auth\Models\User;
use App\Domain\Edduz\DTOs\ProcessWebhookResult;
use App\Domain\Edduz\Enums\WebhookEventType;
use App\Domain\Edduz\Enums\WebhookProcessingStatus;
use App\Domain\Edduz\Repositories\EdduzWebhookLogRepository;
use App\Domain\Subscription\Enums\PlanType;
use App\Domain\Subscription\Repositories\SubscriptionRepository;
use Illuminate\Support\Facades\Log;

class ProcessWebhookAction
{
    public function __construct(
        private EdduzWebhookLogRepository $webhookLogRepo,
        private SubscriptionRepository $subscriptionRepo,
    ) {}

    public function execute(
        array $payload,
        string $ipAddress,
        array $headers,
        string $receivedToken,
    ): ProcessWebhookResult {
        $transactionId = $payload['transaction_id'] ?? null;
        $eventType = $payload['event'] ?? $payload['event_type'] ?? null;
        $userId = $payload['user_id'] ?? null;

        // Step 1a: Check idempotency BEFORE inserting to avoid unique constraint violation
        if ($transactionId !== null) {
            $existing = $this->webhookLogRepo->findByTransactionId($transactionId);
            if ($existing !== null) {
                // Persist a duplicate log (without transaction_id to avoid unique constraint)
                $log = $this->webhookLogRepo->create([
                    'transaction_id' => null,
                    'event_type' => $eventType,
                    'user_id' => null,
                    'payload' => $payload,
                    'ip_address' => $ipAddress,
                    'headers' => $headers,
                    'processing_status' => WebhookProcessingStatus::DUPLICATE->value,
                    'received_at' => now(),
                ]);

                $this->webhookLogRepo->updateProcessingResult(
                    $log->id,
                    WebhookProcessingStatus::DUPLICATE,
                );

                return new ProcessWebhookResult(
                    httpStatus: 200,
                    message: 'Duplicate transaction',
                    processingStatus: WebhookProcessingStatus::DUPLICATE,
                );
            }
        }

        // Step 1b: Persist webhook log FIRST (before any validation)
        $log = $this->webhookLogRepo->create([
            'transaction_id' => $transactionId,
            'event_type' => $eventType,
            'user_id' => null, // FK-safe: user existence is validated later
            'payload' => $payload,
            'ip_address' => $ipAddress,
            'headers' => $headers,
            'processing_status' => WebhookProcessingStatus::ERROR->value,
            'received_at' => now(),
        ]);

        // Step 2: Validate token
        $configuredToken = config('services.edduz.webhook_token');
        if ($receivedToken !== $configuredToken) {
            Log::warning('Edduz webhook: invalid token received', [
                'ip' => $ipAddress,
                'log_id' => $log->id,
            ]);

            $this->webhookLogRepo->updateProcessingResult(
                $log->id,
                WebhookProcessingStatus::INVALID_TOKEN,
                'Invalid webhook token',
            );

            return new ProcessWebhookResult(
                httpStatus: 401,
                message: 'Unauthorized',
                processingStatus: WebhookProcessingStatus::INVALID_TOKEN,
            );
        }

        // Step 4: Map event type
        $webhookEvent = WebhookEventType::tryFrom((string) $eventType);
        if ($webhookEvent === null) {
            $errorMessage = "Unknown event type: {$eventType}";
            Log::warning('Edduz webhook: unknown event type', [
                'event_type' => $eventType,
                'log_id' => $log->id,
            ]);

            $this->webhookLogRepo->updateProcessingResult(
                $log->id,
                WebhookProcessingStatus::ERROR,
                $errorMessage,
            );

            return new ProcessWebhookResult(
                httpStatus: 200,
                message: $errorMessage,
                processingStatus: WebhookProcessingStatus::ERROR,
            );
        }

        // Step 5: Find user
        if ($userId === null || !User::where('id', $userId)->exists()) {
            $errorMessage = "User not found: {$userId}";
            Log::warning('Edduz webhook: user not found', [
                'user_id' => $userId,
                'log_id' => $log->id,
            ]);

            $this->webhookLogRepo->updateProcessingResult(
                $log->id,
                WebhookProcessingStatus::ERROR,
                $errorMessage,
            );

            return new ProcessWebhookResult(
                httpStatus: 200,
                message: $errorMessage,
                processingStatus: WebhookProcessingStatus::ERROR,
            );
        }

        // Step 6: Update subscription based on event type
        try {
            match ($webhookEvent) {
                WebhookEventType::SUBSCRIPTION_CONFIRMED => $this->handleConfirmed($payload, (string) $userId),
                WebhookEventType::SUBSCRIPTION_CANCELLED => $this->subscriptionRepo->cancelSubscription((string) $userId),
                WebhookEventType::SUBSCRIPTION_EXPIRED => User::where('id', $userId)
                    ->update(['subscription_status' => SubscriptionStatus::EXPIRED]),
            };
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error('Edduz webhook: failed to update subscription', [
                'user_id' => $userId,
                'event' => $webhookEvent->value,
                'error' => $errorMessage,
                'log_id' => $log->id,
            ]);

            $this->webhookLogRepo->updateProcessingResult(
                $log->id,
                WebhookProcessingStatus::ERROR,
                $errorMessage,
            );

            return new ProcessWebhookResult(
                httpStatus: 200,
                message: $errorMessage,
                processingStatus: WebhookProcessingStatus::ERROR,
            );
        }

        // Step 7: Update log with SUCCESS
        $this->webhookLogRepo->updateProcessingResult(
            $log->id,
            WebhookProcessingStatus::SUCCESS,
        );

        return new ProcessWebhookResult(
            httpStatus: 200,
            message: 'Webhook processed successfully',
            processingStatus: WebhookProcessingStatus::SUCCESS,
        );
    }

    private function handleConfirmed(array $payload, string $userId): void
    {
        $planId = $payload['plan_id'] ?? 'monthly';
        $transactionId = $payload['transaction_id'] ?? '';

        $planType = match ($planId) {
            'yearly' => PlanType::YEARLY,
            default => PlanType::MONTHLY,
        };

        $this->subscriptionRepo->createSubscription($userId, $planType, (string) $transactionId);
    }
}
