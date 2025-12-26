<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Notification\Notifications\ProfilePushNotification;
use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;
use App\Services\BaseService;

class SendWebPush extends BaseService
{
    public ProfileServiceInterface $profileService;

    public function __construct()
    {
        $this->profileService = app(ProfileServiceInterface::class);
    }

    /**
     * @throws \JsonException
     */
    public function handle($event): void
    {
        $type = $event->type;
        $profile = $this->profileService->findProfileById($event->profileId);

        if ($profile && $type != 'unreadMessageCount' && $type != 'unreadNotificationCount') {
            try {
                $profile->notify(new ProfilePushNotification($event->data, $type));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::channel('notifications')->error(
                    'Failed to send push notification',
                    [
                        'profile_id' => $event->profileId,
                        'type' => $type,
                        'error' => $e->getMessage(),
                        'exception' => get_class($e),
                    ]
                );

                // Не пробрасываем исключение дальше, чтобы не прерывать выполнение
                // Уведомления не критичны для основного функционала
            }
        }
    }
}
