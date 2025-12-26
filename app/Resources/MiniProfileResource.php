<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MiniProfileResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'profile';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws \JsonException
     */
    public function toArray(Request $request): array
    {
        $cachedData = Cache::get('external_profile_' . $this->external_id);
        $profileData = $cachedData ? json_decode($cachedData, true, 512, JSON_THROW_ON_ERROR) : [];

        $cacheKey = 'profile_' . $this->id . '_unread_notifications_count';
        $unreadNotificationsCount = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            if (isset($this->notifications)) {
                return $this->notifications->where('pivot.is_read', false)->count();
            }
            return $this->notifications()->wherePivot('is_read', false)->count();
        });

        return array_merge(parent::toArray($request), [
            'group' => $this->group,
            'spec_name' => $profileData['specName'] ?? null,
            'edu_form' => $profileData['eduFormName'] ?? null,
            'semester' => $profileData['semester'] ?? null,
            'course' => isset($profileData['semester']) ? round($profileData['semester'] / 2) : null,
            'unread_notifications_count' => $unreadNotificationsCount,
        ]);
    }
}
