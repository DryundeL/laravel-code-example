<?php

namespace App\Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware(['api', 'maintenance'])
                ->prefix('api')
                ->group(__DIR__ . '/../Routes/api.php');
        });

    }
}
