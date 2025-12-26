<?php

namespace App\Http\Middleware;

use App\Traits\Dates;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для ограничения доступа в определенное время
 *
 * Блокирует доступ к API в заданный период времени,
 * возвращая HTTP 408 с соответствующим сообщением об ошибке.
 *
 * Используется для технического обслуживания системы в вечернее время.
 */
class TimeRestrictionMiddleware
{
    use Dates;
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.env') === 'local' || config('app.env') === 'dev') {
            return $next($request);
        }

        $isRestrictedTime = $this->isRestrictedTime();

        if ($isRestrictedTime) {
            return response()->json([
                'message' => 'Service temporarily unavailable',
            ], 408);
        }

        return $next($request);
    }
}
