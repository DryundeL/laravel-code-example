<?php

namespace App\Http\Middleware;

use App\Traits\KeysCaseConverter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    use KeysCaseConverter;

    /**
     * Handle an incoming request.
     *
     * Transforms incoming request data to snake_case and outgoing response data to camelCase.
     *
     * @param Request $request
     * @param Closure(Request): Response $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-api-token');

        if ($apiKey && $apiKey === config('inStudy.admin_token')) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
