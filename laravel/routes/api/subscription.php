<?php

use App\Http\Controllers\Api\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/plans', [SubscriptionController::class, 'plans']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/subscription/status', [SubscriptionController::class, 'status']);
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel']);
});
