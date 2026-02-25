<?php

use App\Http\Controllers\Api\EdduzCheckoutController;
use App\Http\Controllers\Api\EdduzWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Edduz API Routes
|--------------------------------------------------------------------------
|
| Rotas de integração com a plataforma Edduz.
| - POST /edduz/checkout: gera URL de checkout (requer autenticação)
| - POST /webhooks/edduz: recebe webhooks da Edduz (público)
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/edduz/checkout', [EdduzCheckoutController::class, 'checkout']);
});

Route::post('/webhooks/edduz', [EdduzWebhookController::class, 'handle']);
