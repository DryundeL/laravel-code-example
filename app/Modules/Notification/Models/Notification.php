<?php

namespace App\Modules\Notification\Models;

use App\Modules\User\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'setting_name',
        'title',
        'data',
        'need_more_data',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'need_more_data' => 'boolean',
        ];
    }

    /**
     * @param bool $isDark
     * @return string
     */
    public function getNotificationIcon(bool $isDark = false): string
    {
        $fileName = $isDark ? 'dark_' . $this->name : $this->name;

        return config('inStudy.yandex_s3_url') . 'icons/notifications/' . $fileName . '.png';
    }

    /**
     * @param Profile|null $currentProfile
     * @return array
     */
    public static function getAllNotificationsInfo(?Profile $currentProfile = null): array
    {
        if (!$currentProfile) {
            $currentProfile = Auth::user();
        }

        $user = $currentProfile->user;
        $allUnreadNotificationsCount = 0;
        $currentProfileNotificationsCount = 0;
        $profilesNotifications = [];

        foreach ($user->profiles as $profile) {
            $unreadNotificationsCount = $profile->notifications()->wherePivot('is_read', false)->count();
            $profilesNotifications[$profile->id]['speciality'] = $profile->group;
            $profilesNotifications[$profile->id]['notifications_count'] = $unreadNotificationsCount;

            if ($profile->id === $currentProfile->id) {
                $currentProfileNotificationsCount = $unreadNotificationsCount;
                $profilesNotifications[$profile->id]['is_current'] = true;
            }

            $allUnreadNotificationsCount += $unreadNotificationsCount;
        }

        return [
            'all_unread_notifications' => $allUnreadNotificationsCount,
            'other_profiles_notifications' => $allUnreadNotificationsCount - $currentProfileNotificationsCount,
            'profiles' => $profilesNotifications
        ];
    }

    /**
     * Получить профили, которым отправлено уведомление
     */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'notification_profile')
            ->withPivot(['text', 'is_read', 'id'])
            ->withTimestamps();
    }
}
