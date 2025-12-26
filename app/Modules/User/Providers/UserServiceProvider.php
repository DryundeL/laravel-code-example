<?php

namespace App\Modules\User\Providers;

use App\Models\User;
use App\Modules\User\Interfaces\DataTransfer\AccessServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\UserESBServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\UserServiceInterface;
use App\Modules\User\Models\Profile;
use App\Modules\User\Observers\ProfileObserver;
use App\Modules\User\Observers\UserObserver;
use App\Modules\User\Services\AccessService;
use App\Modules\User\Services\ProfileService;
use App\Modules\User\Services\UserESBService;
use App\Modules\User\Services\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->register(RouteServiceProvider::class);
        Profile::observe(ProfileObserver::class);
        User::observe(UserObserver::class);

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ProfileServiceInterface::class, ProfileService::class);
        $this->app->bind(AccessServiceInterface::class, AccessService::class);
        $this->app->bind(UserESBServiceInterface::class, UserESBService::class);
    }
}
