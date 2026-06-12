<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->throttleApi('60,1'); // 60 req/min
        
        // Register custom middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'validate.file.upload' => \App\Http\Middleware\ValidateFileUpload::class,
            'api.min.version' => \App\Http\Middleware\ApiMinVersion::class,
        ]);

        // Append X-API-Min-Version header to all API responses
        $middleware->api(append: [
            \App\Http\Middleware\ApiMinVersion::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
