<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Career\CareerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Career API Routes
|--------------------------------------------------------------------------
|
| Rotas públicas para consulta de carreiras e seus simulados
|
*/

Route::prefix('careers')->group(function () {
    // Public routes - anyone can view careers
    Route::get('/', [CareerController::class, 'index']);
    Route::get('/{id}', [CareerController::class, 'show']);
    Route::get('/{id}/exams', [CareerController::class, 'exams']);
});
