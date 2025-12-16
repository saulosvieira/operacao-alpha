<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Ranking\RankingController;

/*
|--------------------------------------------------------------------------
| Ranking API Routes
|--------------------------------------------------------------------------
|
| Rotas para funcionalidades de ranking e classificação de usuários
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Get ranking list (with optional filters)
    Route::get('/ranking', [RankingController::class, 'index']);
    
    // Get current user's position in ranking
    Route::get('/ranking/my-position', [RankingController::class, 'myPosition']);
});
