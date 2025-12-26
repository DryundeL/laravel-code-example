<?php

namespace App\Modules\Chat\Listeners;

use App\Modules\Notification\Events\Notification;
use App\Modules\Notification\Traits\NotificationHelper;
use App\Services\BaseService;
use App\Traits\iESBRequest;
use App\Traits\MessageFormat;

class UpdateUnreadCount extends BaseService
{
    use iESBRequest, MessageFormat, NotificationHelper;

    /**
     * @throws \JsonException
     */
    public function handle($event): void
    {
        $message = $event->message;
        $profile = $event->profile;

        if ($profile) {
            $payloadData = $this->prepareDataToESB(false, profile: $profile);
            $countMessages = $this->handleRequestToESB('getUnreadMessageAmount', $payloadData, 'amount');

            $unreadMessagesCount = [
                'unreadMessages' => $countMessages,
            ];

            event(new Notification($profile->id, 'unreadMessageCount', $unreadMessagesCount));

            $this->sendNotifications($profile, 'message', $message);
        }
    }
}
