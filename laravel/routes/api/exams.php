<?php

use App\Http\Controllers\Api\Exam\ExamController;
use App\Http\Controllers\Api\Exam\AttemptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Exam API Routes
|--------------------------------------------------------------------------
|
| Rotas para gerenciamento de simulados (exams) e tentativas (attempts)
|
*/

Route::prefix('exams')->middleware('auth:sanctum')->group(function () {
    // Exam routes
    Route::get('/', [ExamController::class, 'index']);
    Route::get('/{id}', [ExamController::class, 'show']);
    
    // Attempt routes
    Route::post('/{id}/start', [AttemptController::class, 'start']);
    Route::prefix('attempts')->group(function () {
        Route::get('/{id}', [AttemptController::class, 'show']);
        Route::post('/{id}/answer', [AttemptController::class, 'answer']);
        Route::post('/{id}/finish', [AttemptController::class, 'finish']);
    });
});
