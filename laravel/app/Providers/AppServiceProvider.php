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
    }
}
