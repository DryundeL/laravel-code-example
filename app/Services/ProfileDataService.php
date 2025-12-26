<?php

namespace App\Services;

use App\Modules\User\Interfaces\DataTransfer\UserESBServiceInterface;
use App\Modules\User\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Traits\LazyServiceLoader;

class ProfileDataService
{
    use LazyServiceLoader;

    /**
     * Получает данные профиля из кэша или перезагружает их
     *
     * @param Profile $profile
     * @return array
     */
    public function getProfileData(Profile $profile): array
    {
        $cacheKey = 'external_profile_' . $profile->external_id;
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData || empty($cachedData)) {
            $this->refreshProfileData($profile);
            $cachedData = Cache::get($cacheKey);
        }

        $profileData = json_decode($cachedData, true) ?? [];

        return $this->normalizeProfileData($profileData);
    }

    /**
     * Перезагружает данные профиля из ESB
     *
     * @param Profile $profile
     * @return void
     */
    public function refreshProfileData(Profile $profile): void
    {
        $userESBService = $this->getService(UserESBServiceInterface::class);

        // Получаем профили пользователя из ESB
        $esbProfiles = $userESBService->getProfilesFromESB($profile->user);

        if (is_array($esbProfiles) && !empty($esbProfiles)) {
            // Сохраняем данные в кэш
            $userESBService->setAdditionalInfoForProfilesToRedis($esbProfiles);
        }
    }

    /**
     * Нормализует данные профиля, обеспечивая безопасный доступ к полям
     *
     * @param array $profileData
     * @return array
     */
    private function normalizeProfileData(array $profileData): array
    {
        $fullAccessEndDate = $profileData['fullAccessEndDate'] ?? null;

        return [
            'fullAccessEndDate' => !empty($fullAccessEndDate) ? Carbon::parse($fullAccessEndDate) : null,
            'specName' => $profileData['specName'] ?? null,
            'eduForm' => $profileData['eduFormName'] ?? null,
            'faculty' => $profileData['faculty'] ?? null,
            'program' => $profileData['program'] ?? null,
            'educationProfile' => $profileData['educationProfile'] ?? null,
            'level' => $profileData['level'] ?? null,
        ];
    }
}
