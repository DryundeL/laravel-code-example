<?php

namespace App\Modules\Ai\Providers;

use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }
}
