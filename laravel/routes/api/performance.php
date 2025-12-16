<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Performance\PerformanceController;

/*
|--------------------------------------------------------------------------
| Performance API Routes
|--------------------------------------------------------------------------
|
| Rotas para funcionalidades de desempenho e estatísticas do usuário
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Get user performance statistics
    Route::get('/performance/statistics', [PerformanceController::class, 'statistics']);
    
    // Get user exam history
    Route::get('/performance/history', [PerformanceController::class, 'history']);
});
