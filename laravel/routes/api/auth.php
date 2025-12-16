<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth API Routes
|--------------------------------------------------------------------------
|
| Rotas de autenticação da API usando Laravel Sanctum
|
*/

// Rotas públicas
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

// Rotas protegidas
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
});
