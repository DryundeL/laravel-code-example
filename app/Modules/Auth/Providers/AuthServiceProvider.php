<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->loadViewsFrom(
            base_path('app/Modules/Auth/Resources/Views'),
            'auth'
        );
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }
}
