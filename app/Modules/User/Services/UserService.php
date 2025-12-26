<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\User\Interfaces\DataTransfer\UserServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\UserESBServiceInterface;
use App\Modules\Finance\Interfaces\DataTransfer\FinanceServiceInterface;
use App\Modules\User\Models\Profile;
use App\Services\BaseService;
use App\Jobs\DisciplineCacheJob;
use App\Jobs\SendUserDataToBusJob;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Traits\LazyServiceLoader;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService implements UserServiceInterface
{
    use LazyServiceLoader;

    public function __construct(User $model)
    {
        $this->setModel($model);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findUserById(int $id): ?User
    {
        return $this->find($id);
    }

    public function createUser(array $attributes): User
    {
        return DB::transaction(function () use ($attributes) {
            return $this->model->create([
                'email' => $attributes['email'],
            ]);
        });
    }

    public function updateUserByEmail(array $attributes, string $email): bool|array|null
    {
        $user = $this->model->where('email', $email)->first();

        if (isset($attributes['email']) && $attributes['email'] === $email) {
            unset($attributes['email']);
        }

        DB::transaction(static function () use ($user, $attributes) {
            $user->update($attributes);
            $user->refresh();
        });

        return true;
    }

    /**
     * Получить общее количество пользователей
     *
     * @return int
     */
    public function getTotalUsersCount(): int
    {
        return $this->model->count();
    }

    /**
     * Получить количество пользователей с хотя бы одним профилем
     *
     * @return int
     */
    public function getUsersWithProfilesCount(): int
    {
        return $this->model->whereHas('profiles')->count();
    }

    /**
     * Получить количество новых пользователей с указанной даты
     *
     * @param \Carbon\Carbon $date
     * @return int
     */
    public function getNewUsersCountSince(\Carbon\Carbon $date): int
    {
        return $this->model->where('created_at', '>=', $date)->count();
    }

    /**
     * Получить активность регистраций за последние 7 дней
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWeeklyActivity(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', \Carbon\Carbon::now()->subWeek())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get paginated list of users with search and filters.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getUsersList(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with(['profiles.access'])
            ->withCount('profiles');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchPattern = '%' . $search . '%';

            $query->where(function ($q) use ($searchPattern) {
                $q->where('first_name', 'ILIKE', $searchPattern)
                    ->orWhere('last_name', 'ILIKE', $searchPattern)
                    ->orWhere('middle_name', 'ILIKE', $searchPattern)
                    ->orWhere('email', 'ILIKE', $searchPattern);
            });
        }

        if (!empty($filters['with_profiles'])) {
            $query->has('profiles');
        }

        if (!empty($filters['new_last_24h'])) {
            $query->where('created_at', '>=', now()->subDay());
        }

        if (!empty($filters['group']) || !empty($filters['org']) || !empty($filters['access_id']) || !empty($filters['with_debt'])) {
            $query->whereHas('profiles', function ($q) use ($filters) {
                if (!empty($filters['group'])) {
                    $q->where('group', $filters['group']);
                }
                if (!empty($filters['org'])) {
                    $q->where('org', $filters['org']);
                }
                if (!empty($filters['access_id'])) {
                    $q->where('access_id', $filters['access_id']);
                }
                if (!empty($filters['with_debt'])) {
                    $q->whereNotNull('last_debt')
                        ->where('last_debt', '>', 0);
                }
            });
        }

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = $filters['per_page'] ?? 20;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function getAuthUser(?Request $request = null): array|Authenticatable|null
    {
        /** @var \App\Modules\User\Models\Profile $profile */
        $profile = Auth::user();

        if ($profile->ban) {
            return $this->getErrorMessage('profile', 'Профиль заблокирован');
        }

        $profile->loadMissing(['access.modules', 'access.widgets', 'notifications', 'settings']);
        $user = $profile?->user;

        $userESBService = $this->getService(UserESBServiceInterface::class);
        $financeService = $this->getService(FinanceServiceInterface::class);

        // Кэшируем ESB профили на 30 минут
        $cacheKey = 'user_' . $user->id . '_esb_profiles';
        $iESBProfiles = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user, $userESBService) {
            return $userESBService->getProfilesFromESB($user);
        });

        $userESBService->setAdditionalInfoForProfilesToRedis($iESBProfiles);
        $userESBService->setProfilesFromESB($user, $iESBProfiles);

        // Кэшируем долг на 15 минут
        $debtCacheKey = 'profile_' . $profile->id . '_debt';
        $debt = Cache::remember($debtCacheKey, now()->addMinutes(15), function () use ($profile, $financeService) {
            return $financeService->getProfileDebt($profile);
        });

        if (is_array($debt) && array_key_exists('errors', $debt)) {
            return $debt;
        }

        if ($profile) {
            DB::transaction(function () use ($profile, $debt) {
                $profile->update([
                    'last_debt' => $debt,
                ]);
            });
            $profile->debt = $debt ?? null;
        }

        try {
            DisciplineCacheJob::dispatch($profile);
        } catch (Exception $e) {
            Log::warning('Не удалось запустить job кеширования дисциплин при получении профиля', [
                'profile_id' => $profile->id,
                'error' => $e->getMessage()
            ]);
        }

        if ($request !== null && app()->environment('prod')) {
            try {
                $userId = $profile->external_id;
                $clientIp = getClientIpAddress($request);

                if ($userId) {
                    SendUserDataToBusJob::dispatch(
                        userId: $userId,
                        ip: $clientIp ?? '',
                        agent: $request->userAgent() ?? '',
                        bus: 'setVisit'
                    );
                }
            } catch (Exception $e) {
                Log::warning('Не удалось запустить job отправки данных пользователя на шину при получении профиля', [
                    'profile_id' => $profile->id,
                    'user_id' => $profile->external_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $profile->loadMissing('settings');
        $settings = $profile->settings;

        return [
            'user' => $user,
            'profile' => $profile,
            'accesses' => $user->getAccesses(),
            'notification_settings' => $settings->notification_settings,
            'appearence' => $settings->appearence,
        ];
    }

    /**
     * @throws Exception
     */
    public function getAllProfiles(): array
    {
        $currentProfile = Auth::user();
        $user = $currentProfile?->user;

        $cacheKey = 'user_' . $user->id . '_profiles';
        $profiles = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            return $user->profiles()
                ->notBanned()
                ->with(['access.modules', 'access.widgets', 'notifications'])
                ->get();
        });

        return [
            'user' => $user,
            'profiles' => $profiles,
        ];
    }

    /**
     * @throws Exception
     */
    public function switchProfile(Profile $profile): array
    {
        if ($profile->ban) {
            return $this->getErrorMessage('profile', 'Профиль заблокирован');
        }

        $profile->loadMissing(['access.modules', 'access.widgets', 'notifications', 'settings']);
        $currentProfile = Auth::user();
        $user = $currentProfile?->user;

        if ($profile->id === $currentProfile?->id) {
            return $this->getErrorMessage('profile', 'Профиль уже используется');
        }

        $userESBService = $this->getService(UserESBServiceInterface::class);
        $financeService = $this->getService(FinanceServiceInterface::class);

        // Используем кэшированные ESB профили
        $cacheKey = 'user_' . $user->id . '_esb_profiles';
        $iESBProfiles = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user, $userESBService) {
            return $userESBService->getProfilesFromESB($user);
        });

        $userESBService->setAdditionalInfoForProfilesToRedis($iESBProfiles);
        $userESBService->setProfilesFromESB($user, $iESBProfiles);

        $debtCacheKey = 'profile_' . $profile->id . '_debt';
        $debt = Cache::remember($debtCacheKey, now()->addMinutes(15), function () use ($profile, $financeService) {
            return $financeService->getProfileDebt($profile);
        });

        if (is_array($debt) && array_key_exists('errors', $debt)) {
            return $debt;
        }

        DB::transaction(function () use ($profile, $debt) {
            $profile->update([
                'last_debt' => $debt,
            ]);
        });
        $profile->debt = $debt ?? null;

        $currentProfile?->currentAccessToken()->delete();
        $newToken = $profile->createToken($user->email . '_token')->plainTextToken;

        try {
            if (!$profile->gid) {
                Log::warning('Профиль не содержит поле gid, пропускаем кеширование дисциплин при переключении', [
                    'profile_id' => $profile->id,
                    'gid' => $profile->gid
                ]);
            } else {
                DisciplineCacheJob::dispatch($profile);
            }
        } catch (Exception $e) {
            Log::warning('Не удалось запустить job кеширования дисциплин при переключении профиля', [
                'profile_id' => $profile->id,
                'error' => $e->getMessage()
            ]);
        }

        $settings = $profile->settings;

        return [
            'user' => $user,
            'profile' => $profile,
            'token' => $newToken,
            'accesses' => $user->getAccesses(),
            'notification_settings' => $settings->notification_settings,
            'appearence' => $settings->appearence,
        ];
    }
}
