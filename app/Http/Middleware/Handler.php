<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Configuration\Middleware as BaseMiddleware;

class Handler
{
    protected array $aliases = [
    ];

    protected array $apiMiddlewareGroup = [
        TransformCaseMiddleware::class,
    ];

    protected array $middlewareAliases = [
        'auth' => Authenticate::class,
        'admin' => AdminMiddleware::class,
        'debt.check' => DebtBlockMiddleware::class,
        'discipline.access' => DisciplineAccessMiddleware::class,
        'profile.cache' => ProfileCacheMiddleware::class,
        'time.restriction' => TimeRestrictionMiddleware::class,
        'sentry.tunnel' => SentryTunnelMiddleware::class,
        'maintenance' => MaintenanceMiddleware::class,
    ];

    public function __invoke(BaseMiddleware $middleware): BaseMiddleware
    {
        if ($this->aliases) {
            $middleware->alias($this->aliases);
        }

        $middleware->appendToGroup('api', $this->apiMiddlewareGroup);

        $middleware->alias($this->middlewareAliases);

        return $middleware;
    }
}
