<?php

use App\Http\Controllers\Api\Notification\FcmNotificationController;
use App\Http\Controllers\Api\Notification\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification API Routes
|--------------------------------------------------------------------------
|
| Rotas para gerenciamento de notificações push
|
*/

Route::prefix('notifications')->group(function () {
    // Public route to get VAPID public key
    Route::get('/vapid-public-key', [NotificationController::class, 'getVapidPublicKey'])
        ->name('api.notifications.vapid-key');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/subscribe', [NotificationController::class, 'subscribe'])
            ->name('api.notifications.subscribe');
        Route::post('/unsubscribe', [NotificationController::class, 'unsubscribe'])
            ->name('api.notifications.unsubscribe');
        
        // Admin only - send notifications
        Route::post('/send', [NotificationController::class, 'send'])
            ->middleware('can:admin')
            ->name('api.notifications.send');

        // FCM (Firebase Cloud Messaging) routes
        Route::prefix('fcm')->group(function () {
            Route::post('/subscribe', [FcmNotificationController::class, 'subscribe'])
                ->name('api.notifications.fcm.subscribe');
            Route::post('/unsubscribe', [FcmNotificationController::class, 'unsubscribe'])
                ->name('api.notifications.fcm.unsubscribe');
        });
    });
});
