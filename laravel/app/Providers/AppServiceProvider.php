<?php

namespace App\Providers;

use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );
        $this->app->bind(
            \App\Repositories\AircraftRepositoryInterface::class,
            \App\Repositories\AircraftRepository::class
        );

        $this->app->bind(ClientRepository::class, function ($app) {
            return new ClientRepository($app->make(\App\Models\Client::class));
        });

        $this->app->bind(ClientService::class, function ($app) {
            return new ClientService($app->make(ClientRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar o TableHelper como um helper global
        $this->app->singleton('table.helper', function ($app) {
            return new \App\Helpers\TableHelper();
        });

        // Define Gate for admin access
        \Illuminate\Support\Facades\Gate::define('admin', function ($user) {
            return $user->role === \App\Domain\Auth\Enums\UserRole::ADMIN->value;
        });

        // Configure rate limiting for login
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });
    }
}
