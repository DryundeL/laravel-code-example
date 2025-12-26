<?php

namespace App\Modules\Document\Providers;

use Illuminate\Support\ServiceProvider;

class DocumentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(app_path('Modules/Document/Resources/Views'), 'document');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Document\Interfaces\DataTransfer\DocumentServiceInterface::class,
            \App\Modules\Document\Services\DocumentService::class
        );
    }
}
