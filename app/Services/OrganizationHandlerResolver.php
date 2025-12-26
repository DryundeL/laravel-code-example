<?php

namespace App\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Auth;

class OrganizationHandlerResolver
{
    protected Container $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Получить соответствующий обработчик на основе модуля и организации пользователя.
     *
     * @param string $moduleName
     * @param string|null $handlerGroup
     * @return mixed
     * @throws BindingResolutionException
     */
    public function getHandler(string $moduleName, string $handlerGroup = null): mixed
    {
        $profile = Auth::user();
        $organization = $profile?->org ?? 'default';
        $configPath = "modules.{$moduleName}.organizations";

        $handlers = config($configPath);

        if (array_key_exists($organization, $handlers) && $handlerGroup) {
            return $this->app->make($handlers[$organization][$handlerGroup]);
        }

        if (array_key_exists($organization, $handlers)) {
            return $this->app->make($handlers[$organization]);
        }

        if (!array_key_exists('default', $handlers)) {
            throw new \RuntimeException("No handler default defined in module '{$moduleName}'.");
        }

        return $this->app->make($handlers['default']);
    }
}
