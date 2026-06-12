<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;
use App\Domain\Notification\DTOs\NotificationData;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendNotificationAction
{
    public function __construct(
        private NotificationRepository $repository,
        private FcmSendNotificationAction $fcmAction
    ) {}

    public function execute(string $userId, NotificationData $notification): array
    {
        $webPushResult = $this->executeWebPush($userId, $notification);
        $fcmResult = $this->executeFcm($userId, $notification);

        return $this->aggregateResults($webPushResult, $fcmResult);
    }

    public function sendToAll(NotificationData $notification): array
    {
        $webPushResult = $this->sendToAllWebPush($notification);
        $fcmResult = $this->sendToAllFcm($notification);

        return $this->aggregateResults($webPushResult, $fcmResult);
    }

    private function executeWebPush(string $userId, NotificationData $notification): array
    {
        try {
            $subscriptions = $this->repository->findByUser($userId);

            if ($subscriptions->isEmpty()) {
                return [
                    'success' => false,
                    'sent' => 0,
                    'failed' => 0,
                    'total' => 0,
                ];
            }

            return $this->sendWebPushToSubscriptions($subscriptions, $notification);
        } catch (\Exception $e) {
            \Log::error('Web Push channel failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'total' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function sendToAllWebPush(NotificationData $notification): array
    {
        try {
            $subscriptions = $this->repository->findAll();

            if ($subscriptions->isEmpty()) {
                return [
                    'success' => false,
                    'sent' => 0,
                    'failed' => 0,
                    'total' => 0,
                ];
            }

            return $this->sendWebPushToSubscriptions($subscriptions, $notification);
        } catch (\Exception $e) {
            \Log::error('Web Push channel failed (sendToAll)', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'total' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function sendWebPushToSubscriptions($subscriptions, NotificationData $notification): array
    {
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

    private function executeFcm(string $userId, NotificationData $notification): array
    {
        try {
            return $this->fcmAction->execute($userId, $notification);
        } catch (\Exception $e) {
            \Log::error('FCM channel failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'total' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function sendToAllFcm(NotificationData $notification): array
    {
        try {
            return $this->fcmAction->sendToAll($notification);
        } catch (\Exception $e) {
            \Log::error('FCM channel failed (sendToAll)', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'total' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function aggregateResults(array $webPushResult, array $fcmResult): array
    {
        $totalSent = $webPushResult['sent'] + $fcmResult['sent'];
        $totalFailed = $webPushResult['failed'] + $fcmResult['failed'];
        $totalSubscriptions = $webPushResult['total'] + $fcmResult['total'];

        return [
            'success' => $totalSent > 0,
            'sent' => $totalSent,
            'failed' => $totalFailed,
            'total' => $totalSubscriptions,
            'web_push' => $webPushResult,
            'fcm' => $fcmResult,
        ];
    }
}
