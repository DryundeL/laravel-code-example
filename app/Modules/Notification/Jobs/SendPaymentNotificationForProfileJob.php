<?php

namespace App\Modules\Notification\Jobs;

use App\Modules\Notification\Services\NotificationService;
use App\Services\BaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendPaymentNotificationForProfileJob implements ShouldQueue
{
    use Queueable;

    protected NotificationService $service;

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
        Log::channel('notifications')->info('Start check payment notification for profileId = ' . $this->profileId);
        $this->service->sendPaymentNotification($this->profileId);
    }
}
