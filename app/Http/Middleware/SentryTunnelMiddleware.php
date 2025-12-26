<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class SentryTunnelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Обработка CORS preflight запросов
        if ($request->isMethod('OPTIONS')) {
            return $this->handleCorsPreflight($request);
        }

        // Проверка CORS для POST запросов
        if (!$this->isOriginAllowed($request)) {
            return response()->json([
                'errors' => ['cors' => 'Origin not allowed']
            ], 403);
        }

        // Добавляем CORS заголовки к ответу
        $response = $next($request);
        return $this->addCorsHeaders($request, $response);
    }

    /**
     * Обработка CORS preflight запросов
     *
     * @param Request $request
     * @return Response
     */
    private function handleCorsPreflight(Request $request): Response
    {
        $response = response('', 200);
        return $this->addCorsHeaders($request, $response);
    }

    /**
     * Проверка разрешен ли origin
     *
     * @param Request $request
     * @return bool
     */
    private function isOriginAllowed(Request $request): bool
    {
        $allowedOrigins = config('sentry.cors.allowed_origins', ['*']);

        // Если разрешены все origins
        if (in_array('*', $allowedOrigins)) {
            return true;
        }

        $origin = $request->header('Origin');
        if (!$origin) {
            return true; // Разрешаем запросы без Origin (например, из Postman)
        }

        return in_array($origin, $allowedOrigins);
    }

    /**
     * Добавление CORS заголовков к ответу
     *
     * @param Request $request
     * @param BaseResponse $response
     * @return BaseResponse
     */
    private function addCorsHeaders(Request $request, BaseResponse $response): BaseResponse
    {
        $allowedOrigins = config('sentry.cors.allowed_origins', ['*']);
        $allowedHeaders = config('sentry.cors.allowed_headers', []);
        $allowedMethods = config('sentry.cors.allowed_methods', ['POST', 'OPTIONS']);

        $origin = $request->header('Origin');

        // Определяем Access-Control-Allow-Origin
        if (in_array('*', $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } elseif ($origin && in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        // Добавляем остальные CORS заголовки
        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        $response->headers->set('Access-Control-Allow-Credentials', 'false');
        $response->headers->set('Access-Control-Max-Age', '86400'); // 24 часа

        // Sentry специфичные заголовки
        $response->headers->set('Access-Control-Expose-Headers', 'x-sentry-error,x-sentry-rate-limits,retry-after');
        $response->headers->set('Cross-Origin-Resource-Policy', 'cross-origin');

        return $response;
    }
}
