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
| Rotas do Painel Admin (Blade/Laravel)
|--------------------------------------------------------------------------
*/

// Rotas de autenticação do admin
Route::prefix('admin')->name('admin.')->group(function () {
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

    // Rotas protegidas do admin
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

        // Users Module
        Route::prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('/{user}', [UserController::class, 'show'])->name('show');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/modal', [UserController::class, 'showModal'])->name('modal');
            });

        // Careers
        Route::resource('careers', \App\Http\Controllers\Admin\CareerController::class);
        // Notices
        Route::resource('notices', \App\Http\Controllers\Admin\NoticeController::class);
        // Exams
        Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Rotas do PWA (React SPA)
|--------------------------------------------------------------------------
| 
| Todas as rotas que não começam com /admin ou /api serão servidas pelo PWA.
| O React Router gerencia a navegação client-side.
|
*/

// Rota catch-all para o PWA React (deve ser a última)
Route::get('/{any}', function () {
    return view('app'); // View Blade que carrega o React
})->where('any', '^(?!admin|api).*$')->name('pwa');

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