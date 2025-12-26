<?php

namespace App\Providers;

use App\Modules\Schedule\Services\UniversityEventsCacheService;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Автоматическое обнаружение модульных сервис-провайдеров.
     */
    protected function getModuleServiceProviders(): array
    {
        $providers = [];
        $modulesPath = app_path('Modules');

        if (!is_dir($modulesPath)) {
            return $providers;
        }

        $modules = scandir($modulesPath);

        foreach ($modules as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $providerPath = "{$modulesPath}/{$module}/Providers/{$module}ServiceProvider.php";
            $providerClass = "\\App\\Modules\\{$module}\\Providers\\{$module}ServiceProvider";

            if (file_exists($providerPath) && class_exists($providerClass)) {
                $providers[] = $providerClass;
            }
        }

        return $providers;
    }

    /**
     * Регистрация сервисов приложения.
     */
    public function register(): void
    {
        foreach ($this->getModuleServiceProviders() as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Загрузка сервисов приложения.
     */
    public function boot(): void
    {
        RateLimiter::for('api', static function () {
            return Limit::none();
        });

        Carbon::setLocale('ru');
        App::setLocale('ru');

        // Регистрируем layouts глобально для использования в модульных шаблонах
        // Регистрируем директорию layouts/emails с правильным namespace
        $this->loadViewsFrom(
            resource_path('views/layouts/emails'),
            'layouts.emails'
        );

        // Кеширование университетских событий при запуске приложения
        $this->cacheUniversityEventsOnStartup();
    }

    /**
     * Кеширует университетские события при запуске приложения, если кеш не существует
     */
    private function cacheUniversityEventsOnStartup(): void
    {
        $this->app->make(UniversityEventsCacheService::class)->cacheIfNotExists();
    }
}
