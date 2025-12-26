<?php

namespace App\Modules\Notification\Providers;

use App\Modules\Notification\Commands\GetAutoNotifications;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(Schedule $schedule): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(BroadcastServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadViewsFrom(
            base_path('app/Modules/Notification/Resources/Views'),
            'notification'
        );

        $this->commands(GetAutoNotifications::class);
        $schedule->command(GetAutoNotifications::class)->daily();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }
}
