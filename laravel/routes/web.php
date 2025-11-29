<?php

/**
 * Rotas da aplicação web
 *
 * Este arquivo contém todas as rotas da aplicação web que são carregadas pelo RouteServiceProvider.
 */

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

// Redirecionamento raiz para o dashboard
Route::redirect('/', '/dashboard');

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [
        LoginController::class,
        'showLoginForm',
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login',
    ])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Requerem autenticação)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [
        LoginController::class,
        'logout',
    ])->name('logout');

    // Dashboard
    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

    // Módulo de Usuários
    Route::prefix('usuarios')
        ->name('usuarios.')
        ->group(function () {
            // Listagem de usuários
            Route::get('/', [
                UserController::class,
                'index',
            ])->name('index');

            // Criação de usuário
            Route::get('/create', [
                UserController::class,
                'create',
            ])->name('create');

            Route::post('/', [
                UserController::class,
                'store',
            ])->name('store');

            // Visualização de usuário
            Route::get('/{user}', [
                UserController::class,
                'show',
            ])->name('show');

            // Edição de usuário
            Route::get('/{user}/edit', [
                UserController::class,
                'edit',
            ])->name('edit');

            Route::put('/{user}', [
                UserController::class,
                'update',
            ])->name('update');

            // Exclusão de usuário
            Route::delete('/{user}', [
                UserController::class,
                'destroy',
            ])->name('destroy');

            // Modal de visualização
            Route::get('/{id}/modal', [
                UserController::class,
                'showModal',
            ])->name('modal');
        });

    // Rotas Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        // Carreiras
        Route::resource('carreiras', \App\Http\Controllers\Admin\CarreiraController::class);
        // Editais
        Route::resource('editais', \App\Http\Controllers\Admin\EditalController::class);
        // Simulados
        Route::resource('simulados', \App\Http\Controllers\Admin\SimuladoController::class);
    });
});

// Rota alternativa para servir arquivos do storage em DirectAdmin
// Usa /files/ ao invés de /storage/ para evitar conflito com Apache
Route::get('/files/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);

    if (!file_exists($file)) {
        abort(404, 'Arquivo não encontrado');
    }

    $mimeType = mime_content_type($file);

    return response()->file($file, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=3600',
        'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 3600),
    ]);
})->where('path', '.*')->name('storage.serve');