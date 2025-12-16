<?php

declare(strict_types=1);

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Rotas protegidas para gerenciamento de perfil de usuário
|
*/

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Get user profile
    Route::get('/profile', [UserController::class, 'profile']);
    
    // Update user profile
    Route::put('/profile', [UserController::class, 'updateProfile']);
    
    // Delete user account
    Route::delete('/account', [UserController::class, 'deleteAccount']);
});
