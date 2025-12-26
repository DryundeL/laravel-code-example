<?php

namespace App\Modules\Notification\Commands;

use App\Modules\Notification\Jobs\SendPaymentNotificationForProfileJob;
use App\Modules\Notification\Jobs\SendSessionNotificationForProfileJob;
use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;
use Illuminate\Console\Command;

class GetAutoNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-auto-notifications';

    private ProfileServiceInterface $profileService;

    public function __construct()
    {
        parent::__construct();
        $this->profileService = app(ProfileServiceInterface::class);
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $profiles = $this->profileService->getAllProfiles();

        foreach ($profiles as $profile) {
            SendSessionNotificationForProfileJob::dispatch($profile->id)->delay(now()->addSeconds(5));;
            SendPaymentNotificationForProfileJob::dispatch($profile->id)->delay(now()->addSeconds(5));;
        }
    }
}
