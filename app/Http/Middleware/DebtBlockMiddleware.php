<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use function App\Helpers\getDebtLevel;
use Carbon\Carbon;

class DebtBlockMiddleware
{
    /**
     * Обработка входящего запроса.
     *
     * @param Request $request
     * @param Closure $next
     * @param int|null $maxAllowedLevel Максимальный допустимый уровень блокировки (null = без ограничений)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?int $maxAllowedLevel = null): mixed
    {
        $profile = Auth::user();
        $cacheData = Cache::get('external_profile_' . $profile->external_id);
        $profileData = json_decode($cacheData, true, 512, JSON_THROW_ON_ERROR);
        $fullAccessEndDate = $profileData['fullAccessEndDate'] ? Carbon::parse($profileData['fullAccessEndDate']) : null;

        if ($fullAccessEndDate?->greaterThanOrEqualTo(Carbon::now()->format('Y-m-d'))) {
            return $next($request);
        }

        $debt = (float)Cache::get('profile_' . $profile->id . '_debt');

        if (!$debt) {
            return $next($request);
        }

        $debtLevel = getDebtLevel($debt);

        if ($debtLevel === 0 || ($maxAllowedLevel !== null && $debtLevel <= $maxAllowedLevel)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Доступ ограничен из-за задолженности',
            'debt' => $debt
        ], Response::HTTP_FORBIDDEN);
    }
}
