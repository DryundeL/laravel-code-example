<?php

namespace App\Services;

use App\Models\User;
use App\Modules\User\Models\Profile;
use Illuminate\Support\Facades\Cache;
use App\Modules\Schedule\Services\UniversityEventsCacheService;

class CacheService
{
    /**
     * Очищает кэш профиля при его обновлении
     */
    public function clearProfileCache(Profile $profile): void
    {
        Cache::forget('profile_' . $profile->id . '_debt');
        Cache::forget('profile_' . $profile->id . '_unread_notifications_count');
        Cache::forget('user_' . $profile->user_id . '_profiles');
        Cache::forget('user_' . $profile->user_id . '_esb_profiles');
        Cache::forget('profile_' . $profile->id . '_finances');
        Cache::forget('profile_' . $profile->id . '_contacts');
        Cache::forget('profile_' . $profile->id . 'last_error_description');

        Cache::forget('profile_' . $profile->external_id . '_news_all');
        Cache::forget('profile_' . $profile->external_id . '_news_important');
        Cache::forget('profile_' . $profile->external_id . '_news_diff');
        Cache::forget('profile_' . $profile->external_id . '_news_new');
        Cache::forget('profile_' . $profile->external_id . '_news');
    }

    /**
     * Очищает кэш пользователя при его обновлении
     */
    public function clearUserCache(User $user): void
    {
        Cache::forget('users:' . $user->email . ':accesses');
        Cache::forget('user_' . $user->id . '_profiles');
        Cache::forget('user_' . $user->id . '_esb_profiles');
    }

    /**
     * Очищает кэш уведомлений профиля
     */
    public function clearNotificationCache(Profile $profile): void
    {
        Cache::forget('profile_' . $profile->id . '_unread_notifications_count');
    }

    /**
     * Очищает кэш долга профиля
     */
    public function clearDebtCache(Profile $profile): void
    {
        Cache::forget('profile_' . $profile->id . '_debt');
    }

    /**
     * Очищает ESB кэш пользователя
     */
    public function clearESBCache(User $user): void
    {
        Cache::forget('user_' . $user->id . '_esb_profiles');
    }

    /**
     * Очищает кэш университетских событий
     */
    public function clearUniversityEventsCache(): void
    {
        Cache::forget('university_events');
    }

    /**
     * Полная очистка кеша пользователя при выходе из системы
     */
    public function clearAllUserCache(User $user): void
    {
        // Очищаем основной кеш пользователя
        $this->clearUserCache($user);

        // Очищаем кеш для всех профилей пользователя
        $profiles = $user->profiles()->get();
        foreach ($profiles as $profile) {
            $this->clearProfileCache($profile);
        }
    }

    /**
     * Получает университетские события из кэша
     */
    public function getUniversityEvents(): array
    {
        return Cache::get('university_events', []);
    }

    /**
     * Проверяет, существует ли кэш университетских событий
     */
    public function hasUniversityEventsCache(): bool
    {
        return Cache::has('university_events');
    }

    /**
     * Кеширует университетские события, если кеш не существует
     */
    public function cacheUniversityEventsIfNotExists(): void
    {
        app(UniversityEventsCacheService::class)->cacheIfNotExists();
    }
}
