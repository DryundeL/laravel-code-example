<?php

namespace App\Modules\Notification\Jobs;

use App\Modules\Notification\Services\NotificationService;
use App\Services\BaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSessionNotificationForProfileJob implements ShouldQueue
{
    use Queueable;

    protected BaseService $service;
    private int $profileId;

    public function __construct(int $profileId)
    {
        $this->profileId = $profileId;
        $this->service = app(NotificationService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('notifications')->info('Start check session notification for profileId = ' . $this->profileId);
        $this->service->sendSessionNotification($this->profileId);
    }
}
