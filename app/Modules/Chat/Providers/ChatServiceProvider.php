<?php

namespace App\Modules\Chat\Providers;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(BroadcastServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }
}
