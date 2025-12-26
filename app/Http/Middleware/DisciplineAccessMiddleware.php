<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DisciplineAccessMiddleware
{
    /**
     * Проверяет, имеет ли пользователь доступ к запрашиваемой дисциплине
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $profile = Auth::user();
        $disciplineId = $request->route('disciplineId');

        if (!$disciplineId) {
            return $next($request);
        }

        $cacheKey = "profile_{$profile->id}_discipline_ids";
        $userDisciplineIds = Cache::get($cacheKey, []);

        Log::info('profile_'.$profile->id.'_discipline_ids', ['userDisciplineIds' => $userDisciplineIds]);
        if (empty($userDisciplineIds)) {
            return response()->json([
                'message' => 'Список доступных дисциплин не найден. Пожалуйста, сначала получите список дисциплин'
            ], Response::HTTP_FORBIDDEN);
        }

        if (!in_array((int)$disciplineId, $userDisciplineIds, true)) {
            return response()->json([
                'message' => 'Доступ к данной дисциплине запрещен'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
