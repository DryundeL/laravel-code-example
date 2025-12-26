<?php

namespace App\Modules\Auth\Services;

use App\Jobs\DisciplineCacheJob;
use App\Jobs\SendUserDataToBusJob;
use App\Models\User;
use App\Modules\Finance\Interfaces\DataTransfer\FinanceServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\UserESBServiceInterface;
use App\Modules\User\Interfaces\DataTransfer\UserServiceInterface;
use App\Modules\User\Models\Profile;
use App\Services\BaseService;
use App\Services\CacheService;
use App\Traits\iESBRequest;
use App\Traits\LazyServiceLoader;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService extends BaseService
{
    use iESBRequest, LazyServiceLoader;

    /**
     * Returns user info with token from sso
     *
     * @param array $attributes
     * @param Request|null $request
     * @return array|false
     * @throws ConnectionException
     * @throws \Throwable
     */
    public function getUser(array $attributes, ?Request $request = null): array|false
    {
        $code = $attributes['code'];
        $state = $attributes['state'];

        if ($state === 'desktop') {
            $clientId = config('inStudy.desktop_client_id');
            $clientSecret = config('inStudy.desktop_client_secret');
            $redirectUri = config('inStudy.desktop_sso_redirect_url');
        } elseif ($state === 'mobile') {
            $clientId = config('inStudy.mobile_client_id');
            $clientSecret = config('inStudy.mobile_client_secret');
            $redirectUri = config('inStudy.mobile_sso_redirect_url');
        } else {
            return $this->getErrorMessage('state', 'Неверный state. Напишите в поддержку: techbug@inpsycho.ru');
        }

        Log::info('Client Data:', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
        ]);

        $codeResponse = Http::asForm()->post(config('inStudy.sso_url') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'client_secret' => $clientSecret,
            'code' => $code,
        ]);

        if (!$codeResponse->successful()) {
            Log::error('Failed to get token', ['response' => $codeResponse->json(), 'status' => $codeResponse->status()]);
            return $this->getErrorMessage('token', 'Не удалось получить токен. Напишите в поддержку: techbug@inpsycho.ru');
        }

        $tokenData = $codeResponse->json();
        if (!isset($tokenData['access_token'])) {
            Log::error('Access token missing in response', ['response' => $tokenData]);
            return $this->getErrorMessage('token', 'Токен отсутствует в ответе. Напишите в поддержку: techbug@inpsycho.ru');
        }

        $tokenResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $tokenData['access_token'],
        ])->get(config('inStudy.sso_url') . '/api/user');

        if ($tokenResponse->status() !== 200) {
            return false;
        }

        $userAttr = $tokenResponse->json();

        $userService = $this->getService(UserServiceInterface::class);
        $userESBService = $this->getService(UserESBServiceInterface::class);

        $user = $userService->findUserByEmail($userAttr['email']);

        if (!$user) {
            $user = $userService->createUser($userAttr);
            $user->refresh();
        }

        $iESBProfiles = $userESBService->getProfilesFromESB($user);
        $userESBService->setAdditionalInfoForProfilesToRedis($iESBProfiles);
        $isProfileSet = $userESBService->setProfilesFromESB($user, $iESBProfiles);

        if (is_array($isProfileSet) && array_key_exists('errors', $isProfileSet)) {
            return $isProfileSet;
        }

        $profilesCount = $user->profiles()->notBanned()->count();

        if ($profilesCount === 0) {
            return $this->getErrorMessage('profiles', 'Профиль заблокирован. Напишите в поддержку: techbug@inpsycho.ru');
        }

        if ($profilesCount === 1) {
            $profile = $user->profiles()->notBanned()->first();

            return $this->profileAuth($user, $profile, $request);
        }


        return [
            'user' => $user,
            'profiles' => $user->profiles()
                ->notBanned()
                ->with(['access.modules', 'access.widgets', 'notifications'])
                ->get(),
            'accesses' => $user->getAccesses()
        ];
    }


    public function authByProfile(array $attributes, int $profileId, ?Request $request = null): array
    {
        $profile = Profile::find($profileId);
        $profile->loadMissing(['access.modules', 'access.widgets', 'notifications', 'settings']);
        $user = $profile->user;
        $email = $attributes['email'];

        if ($email !== $user->email || !isset($attributes['email'])) {
            return $this->getErrorMessage('user', 'Пользователь не найден. Напишите в поддержку: techbug@inpsycho.ru');
        }

        if ($profile->ban) {
            return $this->getErrorMessage('profile', 'Профиль заблокирован. Напишите в поддержку: techbug@inpsycho.ru');
        }

        if (!$profile) {
            return $this->getErrorMessage('profile', 'Профили не найдены. Напишите в поддержку: techbug@inpsycho.ru');
        }

        return $this->profileAuth($user, $profile, $request);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return void
     */
    public function logout(): void
    {
        $profile = Auth::user();
        $user = $profile?->user;

        if ($profile && $user) {
            $profile->currentAccessToken()->delete();

            $cacheService = $this->getService(CacheService::class);
            $cacheService->clearAllUserCache($user);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param string $email
     * @return void
     */
    public function logoutUserByEmail(string $email): void
    {
        $userService = $this->getService(UserServiceInterface::class);
        $cacheService = $this->getService(CacheService::class);

        $user = $userService->findUserByEmail($email);

        if ($user) {
            $profiles = $user->profiles()->get();

            foreach ($profiles as $profile) {
                $profile->tokens()->delete();
            }

            $cacheService->clearAllUserCache($user);
        }
    }

    private function profileAuth(User $user, Profile $profile, ?Request $request = null): array
    {
        $profile->loadMissing(['access.modules', 'access.widgets', 'notifications', 'settings']);
        $token = $profile->createToken($user->email . '_token')->plainTextToken;
        $financeService = $this->getService(FinanceServiceInterface::class);
        $debt = $financeService->getProfileDebt($profile);

        if (is_array($debt) && array_key_exists('errors', $debt)) {
            return $debt;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($profile, $debt) {
            $profile->update([
                'last_debt' => $debt,
            ]);
        });
        $profile->debt = $debt ?? null;

        try {
            DisciplineCacheJob::dispatch($profile);
        } catch (\Exception $e) {
            Log::warning('Не удалось запустить job кеширования дисциплин при авторизации', [
                'profile_id' => $profile->id,
                'error' => $e->getMessage()
            ]);
        }

        if ($request !== null && app()->environment('prod')) {
            try {
                $userId = $profile->external_id;
                $clientIp = getClientIpAddress($request);

                SendUserDataToBusJob::dispatch(
                    userId: $userId,
                    ip: $clientIp ?? '',
                    agent: $request->userAgent() ?? '',
                    bus: 'setVisit'
                );
            } catch (\Exception $e) {
                Log::warning('Не удалось запустить job отправки данных пользователя на шину при авторизации', [
                    'profile_id' => $profile->id,
                    'user_id' => $profile->external_id ?? $profile->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $settings = $profile->settings;

        return [
            'user' => $user,
            'profile' => $profile,
            'token' => $token,
            'accesses' => $user->getAccesses(),
            'notification_settings' => $settings->notification_settings,
            'appearence' => $settings->appearence,
        ];
    }
}
