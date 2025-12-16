<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;
use App\Domain\Notification\DTOs\NotificationData;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendNotificationAction
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    public function execute(string $userId, NotificationData $notification): array
    {
        $subscriptions = $this->repository->findByUser($userId);

        if ($subscriptions->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No subscriptions found for user',
                'sent' => 0,
            ];
        }

        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);

        $sent = 0;
        $failed = 0;

        foreach ($subscriptions as $subscriptionData) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $subscriptionData->endpoint,
                    'keys' => [
                        'p256dh' => $subscriptionData->publicKey,
                        'auth' => $subscriptionData->authToken,
                    ],
                ]);

                $payload = json_encode($notification->toArray());

                $report = $webPush->sendOneNotification($subscription, $payload);

                if ($report->isSuccess()) {
                    $sent++;
                } else {
                    $failed++;
                    // If subscription is expired or invalid, remove it
                    if ($report->isSubscriptionExpired()) {
                        $this->repository->delete($subscriptionData->endpoint);
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Failed to send notification', [
                    'error' => $e->getMessage(),
                    'subscription_id' => $subscriptionData->id,
                ]);
            }
        }

        return [
            'success' => $sent > 0,
            'sent' => $sent,
            'failed' => $failed,
            'total' => $subscriptions->count(),
        ];
    }

    public function sendToAll(NotificationData $notification): array
    {
        $subscriptions = $this->repository->findAll();

        if ($subscriptions->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No subscriptions found',
                'sent' => 0,
            ];
        }

        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);

        $sent = 0;
        $failed = 0;

        foreach ($subscriptions as $subscriptionData) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $subscriptionData->endpoint,
                    'keys' => [
                        'p256dh' => $subscriptionData->publicKey,
                        'auth' => $subscriptionData->authToken,
                    ],
                ]);

                $payload = json_encode($notification->toArray());

                $report = $webPush->sendOneNotification($subscription, $payload);

                if ($report->isSuccess()) {
                    $sent++;
                } else {
                    $failed++;
                    if ($report->isSubscriptionExpired()) {
                        $this->repository->delete($subscriptionData->endpoint);
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Failed to send notification', [
                    'error' => $e->getMessage(),
                    'subscription_id' => $subscriptionData->id,
                ]);
            }
        }

        return [
            'success' => $sent > 0,
            'sent' => $sent,
            'failed' => $failed,
            'total' => $subscriptions->count(),
        ];
    }
}
