<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Domain\Notification\Actions\FcmSubscribeAction;
use App\Domain\Notification\Actions\FcmUnsubscribeAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FcmNotificationController extends Controller
{
    public function subscribe(
        Request $request,
        FcmSubscribeAction $action
    ): JsonResponse {
        $request->validate([
            'token' => 'required|string',
            'device_id' => 'required|string',
        ]);

        try {
            $fcmToken = $action->execute(
                $request->user()->id,
                $request->input('token'),
                $request->input('device_id')
            );

            return response()->json([
                'message' => 'Successfully subscribed to FCM notifications',
                'fcm_token' => [
                    'id' => $fcmToken->id,
                    'device_id' => $fcmToken->device_id,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to subscribe to FCM notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function unsubscribe(
        Request $request,
        FcmUnsubscribeAction $action
    ): JsonResponse {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        try {
            $deleted = $action->executeByDeviceId($request->input('device_id'));

            if ($deleted) {
                return response()->json([
                    'message' => 'Successfully unsubscribed from FCM notifications',
                ]);
            }

            return response()->json([
                'message' => 'FCM token not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to unsubscribe from FCM notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
