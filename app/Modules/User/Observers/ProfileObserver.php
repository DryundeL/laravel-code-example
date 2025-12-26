<?php

namespace App\Modules\User\Observers;

use App\Modules\Setting\Interfaces\SettingServiceInterface;
use App\Modules\User\Models\Profile;
use App\Services\CacheService;
use App\Traits\LazyServiceLoader;

class ProfileObserver
{
    use LazyServiceLoader;

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Profile "created" event.
     */
    public function created(Profile $profile): void
    {
        $settingService = $this->getService(SettingServiceInterface::class);
        $settings = $settingService->createSettings();
        $profile->settings()->save($settings);
    }

    /**
     * Handle the Profile "updated" event.
     */
    public function updated(Profile $profile): void
    {
        $this->cacheService->clearProfileCache($profile);
    }

    /**
     * Handle the Profile "deleted" event.
     */
    public function deleted(Profile $profile): void
    {
        $this->cacheService->clearProfileCache($profile);
    }
}
