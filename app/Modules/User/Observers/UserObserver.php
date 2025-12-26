<?php

namespace App\Modules\User\Observers;

use App\Models\User;
use App\Services\CacheService;

class UserObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->cacheService->clearUserCache($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->cacheService->clearUserCache($user);
    }
}
