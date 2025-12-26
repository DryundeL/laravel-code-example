<?php

namespace App\Http\Middleware;

use App\Modules\App\Models\AppSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/maintenance') || $request->is('api/maintenance/*')) {
            return $next($request);
        }

        if (AppSettings::first()->is_maintenance && !Auth::user()?->user->ignore_maintenance) {
            return response()->json([], 503);
        }

        return $next($request);
    }
}
