<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Domain\Notification\Actions\SubscribeToNotificationsAction;
use App\Domain\Notification\Actions\UnsubscribeFromNotificationsAction;
use App\Domain\Notification\Actions\SendNotificationAction;
use App\Domain\Notification\DTOs\NotificationData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function subscribe(
        Request $request,
        SubscribeToNotificationsAction $action
    ): JsonResponse {
        $request->validate([
            'endpoint' => 'required|string',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        try {
            $subscription = $action->execute(
                $request->user()->id,
                $request->only(['endpoint', 'keys'])
            );

            return response()->json([
                'message' => 'Successfully subscribed to notifications',
                'subscription' => [
                    'id' => $subscription->id,
                    'endpoint' => $subscription->endpoint,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to subscribe to notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function unsubscribe(
        Request $request,
        UnsubscribeFromNotificationsAction $action
    ): JsonResponse {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        try {
            $deleted = $action->execute($request->input('endpoint'));

            if ($deleted) {
                return response()->json([
                    'message' => 'Successfully unsubscribed from notifications',
                ]);
            }

            return response()->json([
                'message' => 'Subscription not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to unsubscribe from notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function send(
        Request $request,
        SendNotificationAction $action
    ): JsonResponse {
        $request->validate([
            'user_id' => 'required|string|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'icon' => 'nullable|string',
            'badge' => 'nullable|string',
            'url' => 'nullable|string',
            'data' => 'nullable|array',
        ]);

        try {
            $notification = NotificationData::fromArray($request->only([
                'title',
                'body',
                'icon',
                'badge',
                'url',
                'data',
            ]));

            $result = $action->execute($request->input('user_id'), $notification);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getVapidPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('services.vapid.public_key'),
        ]);
    }
}
