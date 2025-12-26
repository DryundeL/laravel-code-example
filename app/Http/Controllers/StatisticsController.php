<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\User\Models\Profile;
use App\Modules\User\Models\Access;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Показать страницу статистики пользователей
     */
    public function index()
    {
        // Общее количество пользователей
        $totalUsers = User::count();

        // Количество пользователей с хотя бы одним профилем
        $usersWithProfiles = User::whereHas('profiles')->count();

        // Общее количество профилей
        $totalProfiles = Profile::count();

        // Новые пользователи за последние 24 часа
        $newUsersLast24h = User::where('created_at', '>=', Carbon::now()->subDay())->count();

        // Статистика по группам
        $groupStatistics = Profile::select('group', DB::raw('count(*) as count'))
            ->whereNotNull('group')
            ->groupBy('group')
            ->orderBy('count', 'desc')
            ->get();

        // Статистика по уровням доступа
        $accessStatistics = Access::withCount('profiles')
            ->orderBy('profiles_count', 'desc')
            ->get();

        // Статистика по организациям
        $orgStatistics = Profile::select('org', DB::raw('count(*) as count'))
            ->whereNotNull('org')
            ->groupBy('org')
            ->orderBy('count', 'desc')
            ->get();

        // Регистрации пользователей за последние 7 дней
        $weeklyActivity = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Статистика по долгам
        $profilesWithDebt = Profile::whereNotNull('last_debt')
            ->where('last_debt', '>', 0)
            ->count();

        $totalDebtAmount = Profile::whereNotNull('last_debt')
            ->where('last_debt', '>', 0)
            ->sum('last_debt');

        // Статистика по долгам по группам
        $debtByGroups = Profile::select('group', DB::raw('count(*) as count'), DB::raw('sum(last_debt) as total_debt'))
            ->whereNotNull('last_debt')
            ->where('last_debt', '>', 0)
            ->whereNotNull('group')
            ->groupBy('group')
            ->orderBy('count', 'desc')
            ->get();

        // Группа с наибольшим количеством долгов
        $groupWithMostDebts = $debtByGroups->first();

        // Средний размер долга
        $averageDebtAmount = $profilesWithDebt > 0 ? $totalDebtAmount / $profilesWithDebt : 0;

        return view('statistics.index', compact(
            'totalUsers',
            'usersWithProfiles',
            'totalProfiles',
            'newUsersLast24h',
            'groupStatistics',
            'accessStatistics',
            'orgStatistics',
            'weeklyActivity',
            'profilesWithDebt',
            'totalDebtAmount',
            'debtByGroups',
            'groupWithMostDebts',
            'averageDebtAmount'
        ));
    }
}
