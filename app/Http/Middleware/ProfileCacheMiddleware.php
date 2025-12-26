<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ProfileCacheMiddleware
{
    /**
     * Проверяет наличие кэша профиля пользователя.
     * Если кэш отсутствует, возвращает ошибку с просьбой перезагрузить страницу.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $profile = Auth::user();

        if (!$profile) {
            return $next($request);
        }

        $cacheData = Cache::get('external_profile_' . $profile->external_id);

        if (!$cacheData) {
            return response()->json([
                'message' => 'Пожалуйста, перезагрузите страницу для обновления данных'
            ], Response::HTTP_CONFLICT);
        }

        return $next($request);
    }
}
