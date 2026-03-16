<?php

/**
 * Rotas da aplicação web
 *
 * Este arquivo contém todas as rotas da aplicação web que são carregadas pelo RouteServiceProvider.
 */

declare(strict_types=1);

use App\Http\Controllers\Admin\AttemptController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\WebhookHistoryController;
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
        // Questions (nested under exams)
        Route::resource('exams.questions', \App\Http\Controllers\Admin\QuestionController::class)
            ->except(['show']);

        // Question Import - Admin only with enhanced security
        Route::prefix('import')
            ->name('import.')
            ->middleware(['admin'])
            ->group(function () {
                // Import dashboard
                Route::get('/questions', [\App\Http\Controllers\Admin\QuestionImportController::class, 'index'])
                    ->name('questions.index');
                
                // File upload and processing - with file size limits
                Route::post('/upload', [\App\Http\Controllers\Admin\QuestionImportController::class, 'uploadFile'])
                    ->name('upload')
                    ->middleware(['validate.file.upload', 'throttle:10,1']); // Validate file uploads and limit to 10 per minute
                
                // Career mapping workflow
                Route::get('/sessions/{session}/mapping', [\App\Http\Controllers\Admin\QuestionImportController::class, 'showMapping'])
                    ->name('sessions.mapping');
                Route::post('/sessions/{session}/mapping', [\App\Http\Controllers\Admin\QuestionImportController::class, 'processMapping'])
                    ->name('sessions.mapping.process');
                
                // Preview and execution workflow
                Route::get('/sessions/{session}/preview', [\App\Http\Controllers\Admin\QuestionImportController::class, 'showPreview'])
                    ->name('sessions.preview');
                Route::post('/sessions/{session}/execute', [\App\Http\Controllers\Admin\QuestionImportController::class, 'executeImport'])
                    ->name('sessions.execute')
                    ->middleware(['throttle:5,1']); // Limit import execution to 5 per minute
                
                // Session management
                Route::delete('/sessions/{session}/cancel', [\App\Http\Controllers\Admin\QuestionImportController::class, 'cancelSession'])
                    ->name('sessions.cancel');
                
                // AJAX endpoints for dynamic data
                Route::get('/career-exams', [\App\Http\Controllers\Admin\QuestionImportController::class, 'getCareerExams'])
                    ->name('career-exams');
                Route::get('/sessions/{session}/progress', [\App\Http\Controllers\Admin\QuestionImportController::class, 'getImportProgress'])
                    ->name('sessions.progress');
                
                // Reports and downloads
                Route::get('/sessions/{session}/report', [\App\Http\Controllers\Admin\QuestionImportController::class, 'showReport'])
                    ->name('sessions.report');
                Route::get('/sessions/{session}/download-error-log', [\App\Http\Controllers\Admin\QuestionImportController::class, 'downloadErrorLog'])
                    ->name('download-error-log');
                Route::get('/sessions/{session}/affected-exams', [\App\Http\Controllers\Admin\QuestionImportController::class, 'getAffectedExams'])
                    ->name('affected-exams');
                Route::get('/sessions/{session}/export-report', [\App\Http\Controllers\Admin\QuestionImportController::class, 'exportReport'])
                    ->name('export-report');
                Route::get('/statistics', [\App\Http\Controllers\Admin\QuestionImportController::class, 'getImportStatistics'])
                    ->name('statistics');
            });

        // Webhooks
        Route::prefix('webhooks')->name('webhooks.')->group(function () {
            Route::get('/edduz', [WebhookHistoryController::class, 'index'])->name('edduz.index');
            Route::get('/edduz/{id}', [WebhookHistoryController::class, 'show'])->name('edduz.show');
        });

        // Tentativas
        Route::prefix('attempts')->name('attempts.')->group(function () {
            Route::get('/', [AttemptController::class, 'index'])->name('index');
            Route::get('/export', [AttemptController::class, 'export'])->name('export');
            Route::get('/export/count', [AttemptController::class, 'exportCount'])->name('export.count');
            Route::get('/{attempt}', [AttemptController::class, 'show'])->name('show');
        });

        // Reclamações
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [ComplaintController::class, 'index'])->name('index');
            Route::post('/', [ComplaintController::class, 'store'])->name('store');
            Route::patch('/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('update-status');
        });
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