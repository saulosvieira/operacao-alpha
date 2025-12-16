<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Approved\ApprovedController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Approved API Routes
|--------------------------------------------------------------------------
|
| Rotas públicas para consulta de aprovados em concursos
|
*/

Route::prefix('approved')->group(function () {
    // Public routes - anyone can view approved candidates
    Route::get('/', [ApprovedController::class, 'index']);
});
